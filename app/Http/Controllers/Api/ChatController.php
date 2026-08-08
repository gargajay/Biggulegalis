<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatRequest;
use App\Models\Message;
use App\Models\User;
use App\Models\UserAssociation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function memberList(Request $request)
    {
        $associationId = $request->association_id;

        if (empty($associationId)) {
            $associationId = UserAssociation::where('user_id', Auth::id())->value('association_id');
        }

        PublicException::Empty($associationId, 'NOT_FOUND');

        $userIds = UserAssociation::where('association_id', $associationId)
            ->where('status', 1)
            ->pluck('user_id')
            ->toArray();

        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', Auth::id())
            ->select('id', 'full_name', 'image')
            ->latest();

        if (!empty($request->search)) {
            $users->where('full_name', 'LIKE', '%' . $request->search . '%');
        }

        $data = newPagination($users);
        foreach ($data['data'] as $user) {
            $chatRequest = $this->chatRequestWith($user->id);
            $user->chat_request_status = $chatRequest ? $chatRequest->status : null;
            $user->chat_id = $chatRequest ? $chatRequest->chat_id : null;
        }

        return Helper::SuccessReturn($data, 'MEMBER_FETCH');
    }

    public function sendRequest(Request $request)
    {
        PublicException::Validator($request->all(), [
            'receiver_id' => ['required', 'exists:users,id'],
        ]);

        if ((int) $request->receiver_id === Auth::id()) {
            PublicException::Error('INVALID_REQUEST');
        }

        if (!$this->isAssociationMember($request->receiver_id)) {
            PublicException::Error('NOT_FOUND');
        }

        $chatRequest = $this->chatRequestWith($request->receiver_id);

        if (!empty($chatRequest)) {
            if ((int) $chatRequest->status === 2) {
                $chatRequest->requester_id = Auth::id();
                $chatRequest->receiver_id = $request->receiver_id;
                $chatRequest->status = 0;
                $chatRequest->chat_id = null;
                PublicException::NotSave($chatRequest->save());
                $chatRequest->load('requester', 'receiver');
                $this->emitSocketRequestUpdate($chatRequest);
            }

            return Helper::SuccessReturn($chatRequest->load('requester', 'receiver'), 'CHAT_REQUEST_SENT');
        }

        $chatRequest = ChatRequest::create([
            'requester_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'status' => 0,
        ]);
        $chatRequest->load('requester', 'receiver');
        $this->emitSocketRequestUpdate($chatRequest);

        return Helper::SuccessReturn($chatRequest, 'CHAT_REQUEST_SENT');
    }

    public function requestList(Request $request)
    {
        $type = $request->type ?? 'received';

        $requests = ChatRequest::with('requester', 'receiver')
            ->when($type === 'sent', function ($query) {
                $query->where('requester_id', Auth::id());
            }, function ($query) {
                $query->where('receiver_id', Auth::id());
            })
            ->latest();

        $data = newPagination($requests);

        return Helper::SuccessReturn($data, 'CHAT_REQUEST_FETCHED');
    }

    public function requestAction(Request $request)
    {
        PublicException::Validator($request->all(), [
            'request_id' => ['required', 'exists:chat_requests,id'],
            'status' => ['required', 'in:1,2'],
        ]);

        $chatRequest = ChatRequest::where('id', $request->request_id)
            ->where('receiver_id', Auth::id())
            ->where('status', 0)
            ->first();

        PublicException::Empty($chatRequest, 'NOT_FOUND');

        $chatRequest->status = (int) $request->status;

        if ((int) $request->status === 1 && empty($chatRequest->chat_id)) {
            $chat = Chat::create([
                'sender_id' => $chatRequest->requester_id,
                'receiver_id' => $chatRequest->receiver_id,
                'model' => User::class,
                'model_id' => $chatRequest->receiver_id,
                'type' => 1,
            ]);

            $chatRequest->chat_id = $chat->id;
        }

        PublicException::NotSave($chatRequest->save());
        $chatRequest->load('requester', 'receiver', 'chat');
        $this->emitSocketRequestUpdate($chatRequest);

        return Helper::SuccessReturn($chatRequest, 'CHAT_REQUEST_UPDATED');
    }

    public function chatList()
    {
        $chatIds = ChatRequest::where('status', 1)
            ->where(function ($query) {
                $query->where('requester_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->pluck('chat_id')
            ->filter()
            ->toArray();

        $chats = Chat::whereIn('id', $chatIds)
            ->with('sender_detail')
            ->latest('updated_at')
            ->get();

        $chats->map(function ($chat) {
            $otherUserId = $chat->sender_id === Auth::id() ? $chat->receiver_id : $chat->sender_id;
            $chat->member = User::where('id', $otherUserId)->select('id', 'full_name', 'image')->first();
            $chat->last_message = Message::where('chat_id', $chat->id)->latest()->first();
            $chat->unread_count = Message::where('chat_id', $chat->id)
                ->where('receiver_id', Auth::id())
                ->where('is_seen', false)
                ->count();
            return $chat;
        });

        return Helper::SuccessReturn($chats, 'CHAT_FETCHED');
    }

    public function messages(Request $request)
    {
        PublicException::Validator($request->all(), [
            'chat_id' => ['required', 'exists:chats,id'],
        ]);

        $this->checkChatAccess($request->chat_id);

        Message::where('chat_id', $request->chat_id)
            ->where('receiver_id', Auth::id())
            ->update(['is_seen' => true]);

        $messages = newPagination(
            Message::with('sender_detail', 'receiver_detail')
                ->where('chat_id', $request->chat_id)
                ->latest()
        );

        return Helper::SuccessReturn($messages, 'CHAT_DETAILS');
    }

    public function sendMessage(Request $request)
    {
        PublicException::Validator($request->all(), [
            'chat_id' => ['required', 'exists:chats,id'],
            'message' => ['nullable', 'string'],
            'media_type' => ['nullable', 'in:text,image,document,link'],
            'media' => ['nullable', 'file'],
        ]);

        $chat = $this->checkChatAccess($request->chat_id);

        if (empty($request->message) && !$request->hasFile('media')) {
            PublicException::Error('INVALID_REQUEST');
        }

        $receiverId = $chat->sender_id === Auth::id() ? $chat->receiver_id : $chat->sender_id;
        $mediaExtension = $request->hasFile('media') ? $request->file('media')->getClientOriginalExtension() : null;
        $mediaType = $this->resolveMessageMediaType($request);
        $mediaPath = Helper::FileUpload('media', CHAT_IMAGE_INFO);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'media_type' => $mediaType,
            'media_url' => $mediaPath,
            'media_extension' => $mediaExtension,
            'is_seen' => false,
        ]);

        $message->load('sender_detail', 'receiver_detail');
        $receiverInChat = $this->emitSocketMessage($message);

        if (!$receiverInChat) {
            $this->sendNewMessageNotification($message);
        }

        return Helper::SuccessReturn($message, 'MESSAGE_SENT');
    }

    private function resolveMessageMediaType(Request $request): string
    {
        if (!$request->hasFile('media')) {
            return $request->media_type ?: 'text';
        }

        $mimeType = $request->file('media')->getClientMimeType();

        if (is_string($mimeType) && str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        return 'document';
    }

    public function socketAuth()
    {
        return Helper::SuccessReturn([
            'user_id' => Auth::id(),
        ], 'USER_FETCH');
    }

    private function chatRequestWith($userId)
    {
        return ChatRequest::where(function ($query) use ($userId) {
            $query->where('requester_id', Auth::id())->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('requester_id', $userId)->where('receiver_id', Auth::id());
        })->latest()->first();
    }

    private function isAssociationMember($userId)
    {
        $associationIds = UserAssociation::where('user_id', Auth::id())
            ->where('status', 1)
            ->pluck('association_id')
            ->toArray();

        if (empty($associationIds)) {
            return false;
        }

        return UserAssociation::where('user_id', $userId)
            ->where('status', 1)
            ->whereIn('association_id', $associationIds)
            ->exists();
    }

    private function checkChatAccess($chatId)
    {
        $chat = Chat::find($chatId);

        PublicException::Empty($chat, 'CHAT_NOT_FOUND');

        if ($chat->sender_id !== Auth::id() && $chat->receiver_id !== Auth::id()) {
            PublicException::Error('UNAUTHORIZED_ACCESS');
        }

        $accepted = ChatRequest::where('chat_id', $chat->id)->where('status', 1)->first();
        PublicException::Empty($accepted, 'MESSAGE_NOT_SEND');

        return $chat;
    }

    private function emitSocketMessage($message): bool
    {
        $url = config('services.socket.url');
        $secret = config('services.socket.secret');

        if (empty($url) || empty($secret)) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'x-socket-secret' => $secret,
            ])->post(rtrim($url, '/') . '/emit-message', [
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message,
            ]);

            return (bool) data_get($response->json(), 'receiver_in_chat', false);
        } catch (\Exception $e) {
            report($e);
        }

        return false;
    }

    private function sendNewMessageNotification(Message $message): void
    {
        try {
            $senderName = $message->sender_detail->full_name ?? Auth::user()->full_name ?? 'New message';
            $body = $message->message ?: match ($message->media_display_type) {
                'image' => 'Sent an image',
                'document' => 'Sent a document',
                default => 'Sent a message',
            };

            PushNotification::Notification([[
                'receiver_id' => [$message->receiver_id],
                'title' => [$senderName],
                'body' => [$body],
                'type' => 'NEW_MESSAGE',
                'app_notification_data' => [
                    'chat_id' => $message->chat_id,
                    'message_id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'media_type' => $message->media_type,
                    'media_display_type' => $message->media_display_type,
                ],
                'push_notification_data' => [
                    'chat_id' => $message->chat_id,
                    'message_id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'media_type' => $message->media_type,
                    'media_display_type' => $message->media_display_type,
                ],
                'model_id' => $message->id,
                'model_name' => Message::class,
            ]], true, true, $message->sender_id);
        } catch (\Exception $exception) {
            Log::warning('New message notification failed.', [
                'message_id' => $message->id,
                'receiver_id' => $message->receiver_id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function emitSocketRequestUpdate($chatRequest)
    {
        $url = config('services.socket.url');
        $secret = config('services.socket.secret');

        if (empty($url) || empty($secret)) {
            return;
        }

        try {
            Http::withHeaders([
                'x-socket-secret' => $secret,
            ])->post(rtrim($url, '/') . '/emit-chat-request', [
                'requester_id' => $chatRequest->requester_id,
                'receiver_id' => $chatRequest->receiver_id,
                'chat_request' => $chatRequest,
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
