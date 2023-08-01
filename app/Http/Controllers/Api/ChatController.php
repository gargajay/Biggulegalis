<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\BusinessNetwork;
use App\Models\Chat;
use App\Models\ChatArchive;
use App\Models\FavouriteChat;
use App\Models\Message;
use App\Models\ReportUser;
use App\Models\User;
use App\Models\UserChatGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function chatList(Request $request)
    {
        $userId = Auth::id();
        $chatObj = Chat::with(['sender_detail' => function ($query) {
            $query->latest();
        }])->where(function ($query) use ($userId) {
            $query->where(function ($query) use ($userId) {
                $query->where('type', 1)->where(function ($query2) use ($userId) {
                    $query2->where('sender_id', $userId)->orWhere('receiver_id', $userId);
                });
            })->orWhere(function ($query) use ($userId) {
                $query->where('type', 2)->where('sender_id', $userId);
            });
        })->orderBy('updated_at', 'desc');
        $count = $chatObj->count();
        $chatObj = $chatObj->paginate(10);
        $data = $chatObj->items();
        $totalPages = $count > 0 ? $chatObj->lastPage() : 0;
        $nextPageUrl = $chatObj->nextPageUrl();
        foreach ($chatObj as &$chatObj2) {
            $chatObj2->sender_detail;
            $chatObj2->receiver_detail = $chatObj2->receiver_detail();
            $chatObj2->last_message = $chatObj2->last_message();
        }
        return Helper::SuccessReturnPagination($data, $totalPages, $nextPageUrl, 'CHAT_FETCHED');
    }

    public function chatDetail(Request $request)
    {
        $rules = [
            'type' => ['required', 'integer', 'in:1,2'], // 1 for one to one chat , 2 for group chat
            'receiver_id' => ['required', 'integer'],
        ];
        if ($request->has('type') && $request->type == 1) {
            $rules['receiver_id'][] = 'iexists:users,id';
        }
        if ($request->has('type') && $request->type == 2) {
            $rules['receiver_id'][] = 'iexists:chat_groups,id';
        }
        PublicException::Validator($request->all(), $rules);
        $chatObj = Chat::where('type', $request->type);
        if ($request->type == 1) {
            $chatObj = $chatObj->where(function ($query) use ($request) {
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $request->receiver_id);
            })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', $request->receiver_id)
                        ->where('receiver_id', Auth::id());
                })
                ->first();
            if (IsEmpty($chatObj)) {
                return Helper::SuccessReturn([], 'CHAT_NOT_FOUND');
            }
            $messageObject = Message::where('chat_id', $chatObj->id);
        } else {
            $chatObj = $chatObj->where('receiver_id', $request->receiver_id)->first();
            if (IsEmpty($chatObj)) {
                return Helper::SuccessReturn([], 'CHAT_NOT_FOUND');
            }
            $chatIdsBasedOnGroup = Chat::where('receiver_id', $chatObj->receiver_id)->pluck('id');
            $messageObject = Message::whereIn('chat_id', $chatIdsBasedOnGroup);
        }
        $messageObject = newPagination($messageObject->with('sender_detail', 'receiver_detail')->orderBy('id', 'desc'), 40);
        return Helper::SuccessReturnPagination($messageObject['data'], $messageObject['totalPages'], $messageObject['nextPageUrl'], 'CHAT_DETAILS');
    }

    public function saveMessage(Request $request)
    {
        $rules = [
            'receiver_id' => ['required', 'integer'],
            'chat_type' => ['required', 'integer', 'in:1,2'], // 1 for one to one chat , 2 for group chat
            'file' => ['nullable', 's3_file_exists'],
        ];

        if (isset($request->type) && $request->type == "text") {
            $rules['message'] = "required";
        }
        PublicException::Validator($request->all(), $rules);
        $userId = Auth::id();
        $sendToId = $request->receiver_id;
        DB::beginTransaction();
        //Check chat exist or not
        if ($request->chat_type == 1) {
            $chatObj = Chat::where(['sender_id' => $userId, 'receiver_id' => $sendToId, 'type' => 1])
                ->orWhere(function ($query) use ($sendToId, $userId) {
                    $query->where(['sender_id' => $sendToId, 'receiver_id' => $userId, 'type' => 1]);
                })->first();
        } else {
            $chatObj = Chat::where(['sender_id' => $userId, 'receiver_id' => $sendToId, 'type' => 2])->first();
        }
        if (IsEmpty($chatObj) && $request->chat_type == 1) {
            //inilitize chat
            $chatObj = new Chat;
            $chatObj->sender_id = $userId;
            $chatObj->receiver_id = $sendToId;
            $chatObj->model = get_class(new User);
            $chatObj->model_id = $userId;
            $chatObj->type = 1;
            PublicException::NotSave($chatObj->save());
        }

        if ($chatObj) {
            $chatId = $chatObj->id;
            $messageObj = new Message;
            $messageObj->chat_id = $chatId;
            $messageObj->sender_id = $userId;
            $messageObj->receiver_id = $sendToId;
            $messageObj->message = $request->message ?? "";
            $messageObj->media_type = $request->type ?? 'text';
            if ($request->type != "text") {
                $messageObj->media_type = $request->type;
                $messageObj->media_extension = $request->file_type ?? "";
                $messageObj->media_url = $request->file;
                $messageObj->media_size = $request->size;
            }
            $messageObj->save();
            $newImagePath = Helper::MoveS3BucketFile($messageObj->getRawOriginal('media_url'), CHAT_IMAGE_INFO);
            if ($newImagePath) {
                $messageObj->media_url = $newImagePath;
                PublicException::NotSave($messageObj->save());
            }
            $receiver_id = [];
            if ($chatObj->type == 1) {
                $receiver_id[] = $sendToId;
                $receiverForSocket[] = $sendToId . '-' . $userId;
            }

            if ($chatObj->type == 2) {
                $group_id = $chatObj->receiver_id;
                $receiver_id = UserChatGroup::where('chat_group_id', $group_id)->where('user_id', '!=', $userId)->pluck('user_id')->toArray();
            }
            $senderName = User::find($userId)->full_name;
            foreach ($receiver_id as $receiver) {
                $notificationData = [[
                    'receiver_id' => $receiver,
                    'title' => ['New Message'],
                    'body' => [($request->type == "file" ? 'SEND_ATTACHMENT' : 'SEND_MESSAGE') => ['name' => $senderName]],
                    'type' => 'chat_request',
                    'app_notification_data' => [($request->type == "file" ? 'SEND_ATTACHMENT' : 'SEND_MESSAGE') => ['name' => $senderName]],
                    'model_id' => $messageObj->id,
                    'model_name' => get_class($messageObj),
                ]];
                PushNotification::Notification($notificationData, false, false, $userId);
            }

            // when new message come then chat show on both side
            if (!$chatObj->sender_chat_hide || !$chatObj->receiver_chat_hide) {
                $chatObj->update(['sender_chat_hide' => 1, 'receiver_chat_hide' => 1]);
            }

            DB::commit();
            $messageObj = Message::find($messageObj->id);
            return Helper::SuccessReturn($messageObj->load('sender_detail', 'receiver_detail'), 'MESSAGE_SENT', receiverIds: $receiverForSocket);
        } else {
            return Helper::EmptyReturn('SOMETHING_WENT_WRONG');
        }
    }

    public function blockUnblockUser(Request $request)
    {
        $rules = [
            'user_id' => ['required', 'integer', 'iexists:users,id'],
        ];
        // Validate the user input data
        $loggedinId = Auth::id();
        PublicException::Validator($request->all(), $rules);
        $checkBlock = BlockedUser::where(['blocked_user_id' => $request->user_id, 'blockedBy_user_id' => $loggedinId])->first();
        if (!empty($checkBlock)) {
            $checkBlock->delete();
            return Helper::SuccessReturn(null, 'USER_UNBLOCKED');
        } else {
            $block = new BlockedUser;
            $block->blocked_user_id = $request->user_id;
            $block->blockedBy_user_id = $loggedinId;
            // if data not save show error
            PublicException::NotSave($block->save());
            return Helper::SuccessReturn(null, 'USER_BLOCKED');
        }
    }

    public function getBlockedUser()
    {
        $blockedUsers = newPagination(BlockedUser::with('user')->where('blockedBy_user_id', Auth::id())->latest());
        return Helper::SuccessReturnPagination($blockedUsers['data'], $blockedUsers['totalPages'], $blockedUsers['nextPageUrl'], 'USER_BLOCKED_LIST');
    }
}
