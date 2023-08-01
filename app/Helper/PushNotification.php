<?php

namespace App\Helper;

use App\Exceptions\PublicException;
use App\Models\Notification;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class PushNotification
{
    /* Example for push notification
    $data = [
        [
            'receiver_id' => [1, 2, 3],
            'title' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'body' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'type' => 'notification',          
            'push_notification_data' => ['foo' => 'bar']
        ],
        [
            'receiver_id' => [4],
            'title' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'body' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'type' => 'notification',
            'push_notification_data' => ['foo' => 'bar']
        ]
    ];
    */

    public static function PushNotification(array $data = null, $sendby = null)
    {
        $apiKey = config('settings.push_notification.key');
        if (empty($apiKey)) {
            PublicException::Error('PUSH_NOTIFICATION_CREDENTIALS');
        }

        if (!$data) {
            return null;
        }

        $userIds = [];
        foreach ($data as $key => $value) {
            if (empty($value['receiver_id'])) {
                throw new PublicException(sprintf('Receiver ID not provided or empty in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['title']) || !is_array($value['title'])) {
                throw new PublicException(sprintf('Title is required in array format in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['body']) || !is_array($value['body'])) {
                throw new PublicException(sprintf('Body is required in array format in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['type'])) {
                throw new PublicException(sprintf('Type not provided or empty in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }

            $receiverIds = (array) $value['receiver_id'];
            $userIds = array_merge($userIds, $receiverIds);
            $data[$key]['receiver_id'] = $receiverIds;
        }

        $users = User::whereIn('id', $userIds)
            ->where('push_notification', '1')
            ->where('device_token', '!=', '')
            ->get()->toArray();

        $users = array_combine(array_column($users, 'id'), $users);

        $client = new Client([
            'headers' => [
                'Authorization' => 'key=' . $apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        $responses = [];
        foreach ($data as $value) {
            $deviceTokens = [];

            foreach ($value['receiver_id'] as $receiverId) {
                if (isset($users[$receiverId])) {
                    $language = $users[$receiverId]['language'] ?? 'en';

                    if (!isset($deviceTokens[$language])) {
                        $deviceTokens[$language] = [];
                    }
                    $deviceTokens[$language][] = $users[$receiverId]['device_token'];
                }
            }

            if (!$deviceTokens) {
                continue;
            }

            foreach ($deviceTokens as $lang => $deviceTokenArray) {
                $title = customTrans($value['title'], $lang);
                $body = customTrans($value['body'], $lang);
                // Split the array into chunks of 1000 values each
                $chunks = array_chunk($deviceTokenArray, 1000);
                foreach ($chunks as $chunk) {
                    $finalData = [
                        'registration_ids' => $chunk,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'title' => $title,
                            'body' => $body,
                            'sendby' => $sendby ?? Auth::check() ? Auth::id() : 0,
                            'data' => json_encode($value['push_notification_data'] ?? []),
                            'content-available' => 1,
                            'badge' => 1,
                            'sound' => 'default',
                            'priority' => 10,
                            'icon' => 'default'
                        ]
                    ];

                    $response = $client->post('https://fcm.googleapis.com/fcm/send', ['json' => $finalData]);
                    $responses[] = $response->getBody()->getContents();
                }
            }
        }

        return $responses;
    }



    /* Example for app notification
    $data = [
        [
            'receiver_id' => [1, 2, 3],
            'title' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'body' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'type' => 'notification',
            'app_notification_data' => ['foo' => 'bar']
        ],
        [
            'receiver_id' => [4],
            'title' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'body' => ['English title','OTP_SEND_SUCCESS','FORGOT_EMAIL_RESEND'=>['seconds'=>'5']],
            'type' => 'notification',
            'app_notification_data' => ['foo' => 'bar']
        ]
    ];
    */

    public static function AppNotification(array $data = null, $sendby = null)
    {
        if (!$data) {
            return null;
        }

        $userIds = [];
        foreach ($data as $key => $value) {

            if (empty($value['receiver_id'])) {
                throw new PublicException(sprintf('Receiver ID not provided or empty in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['title']) || !is_array($value['title'])) {
                throw new PublicException(sprintf('Title is required in array format in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['body']) || !is_array($value['body'])) {
                throw new PublicException(sprintf('Body is required in array format in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }
            if (empty($value['type'])) {
                throw new PublicException(sprintf('Type not provided or empty in push notification data array at key %d.', $key + 1), STATUS_GENERAL_ERROR);
            }

            $receiverIds = (array) $value['receiver_id'];
            $userIds = array_merge($userIds, $receiverIds);
            $data[$key]['receiver_id'] = $receiverIds;
        }

        $users = User::whereIn('id', $userIds)->get()->toArray();
        $users = array_combine(array_column($users, 'id'), $users);
        foreach ($data as $value) {
            foreach ($value['receiver_id'] as $receiverId) {
                if (isset($users[$receiverId])) {

                    $notificationObject = new Notification();

                    $notificationObject->send_by = $sendby ?? Auth::check() ? Auth::id() : 0;
                    $notificationObject->received_by = $receiverId;
                    $notificationObject->title = $value['title'];
                    $notificationObject->body = $value['body'];
                    $notificationObject->data = $value['app_notification_data'] ?? [];
                    $notificationObject->type = $value['type'];
                    $notificationObject->model_id = $value['model_id'];
                    $notificationObject->model_name = $value['model_name'];
                    PublicException::NotSave($notificationObject->save());
                }
            }
        }

        return true;
    }


    public static function Notification(array $data = null, bool $sendAppNotification = false, bool $sendPushNotification = false, $sendby = null)
    {
        $response = null;

        if (!$data) {
            return null;
        }

        if ($sendAppNotification) {
            $response = self::AppNotification($data, $sendby);
        }

        if ($sendPushNotification) {
            $response = self::PushNotification($data, $sendby);
        }

        return $response;
    }
}
