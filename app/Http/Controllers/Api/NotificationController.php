<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        $notificationObject = Notification::where('received_by', Auth::id())->where('read', false)->orderBy('id', 'desc');
        $notificationObject = newPagination($notificationObject);
        return Helper::SuccessReturn($notificationObject, 'NOTIFICATION_FETCHED');
    }


    public function readNotification(Request $request)
    {
        // validate rules for input
        $rules = [
            'notification_id' => ['required_without:read_all', 'numeric', 'positive_integer', 'iexists:notifications,id,received_by,' . Auth::user()->id],
            'read_all' => ['nullable', 'boolean'],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        if ($request->boolean('read_all')) {
            Notification::where('received_by', Auth::id())->where('read', false)->update(['read' => true]);

            return Helper::SuccessReturn(null, 'NOTIFICATION_READ');
        }

        $notificationObject = Notification::where('received_by', Auth::id())->findOrFail($request->notification_id);

        $notificationObject->read = true;

        // if data not save show error
        PublicException::NotSave($notificationObject->save());

        return Helper::SuccessReturn($notificationObject, 'NOTIFICATION_READ');
    }

    public function testPushNotification(Request $request)
    {
        PublicException::Validator($request->all(), [
            'device_token' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (!empty($request->device_token)) {
            $user->device_token = $request->device_token;
            $user->push_notification = '1';
            PublicException::NotSave($user->save());
        }

        if (empty($user->device_token)) {
            return response()->json([
                'success' => false,
                'status' => STATUS_GENERAL_ERROR,
                'message' => 'Device token is required.',
                'data' => null,
            ], STATUS_OK);
        }

        $response = PushNotification::Notification([[
            'receiver_id' => [$user->id],
            'title' => [$request->title ?: 'Firebase test notification'],
            'body' => [$request->body ?: 'FCM v1 test push sent successfully.'],
            'type' => 'TEST_PUSH_NOTIFICATION',
            'app_notification_data' => [
                'notification_type' => 'TEST_PUSH_NOTIFICATION',
                'user_id' => (string) $user->id,
            ],
            'push_notification_data' => [
                'notification_type' => 'TEST_PUSH_NOTIFICATION',
                'click_action' => 'TEST_PUSH_NOTIFICATION',
                'user_id' => (string) $user->id,
            ],
            'model_id' => $user->id,
            'model_name' => get_class($user),
        ]], true, true, $user->id);

        return response()->json([
            'success' => true,
            'status' => STATUS_OK,
            'message' => 'Test push notification sent.',
            'data' => [
                'user_id' => $user->id,
                'device_token_saved' => !empty($user->device_token),
                'firebase_response' => $response,
            ],
        ], STATUS_OK);
    }
}
