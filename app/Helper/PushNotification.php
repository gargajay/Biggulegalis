<?php

namespace App\Helper;

use App\Exceptions\PublicException;
use App\Models\Notification;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        $credentials = self::firebaseCredentials();

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

        $accessToken = self::firebaseAccessToken($credentials);
        $client = new Client([
            'base_uri' => 'https://fcm.googleapis.com',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
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
                foreach ($deviceTokenArray as $deviceToken) {
                    $customData = self::stringifyData($value['push_notification_data'] ?? []);
                    $finalData = [
                        'message' => [
                            'token' => $deviceToken,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => array_merge($customData, [
                                'title' => (string) $title,
                                'body' => (string) $body,
                                'sendby' => (string) ($sendby ?? (Auth::check() ? Auth::id() : 0)),
                                'data' => json_encode($value['push_notification_data'] ?? []),
                                'content-available' => '1',
                                'type' => (string) $value['type'],
                                'badge' => '1',
                                'sound' => 'default',
                                'priority' => 'high',
                                'icon' => 'default',
                            ]),
                            'android' => [
                                'priority' => 'HIGH',
                                'notification' => [
                                    'sound' => 'default',
                                    'default_sound' => true,
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound' => 'default',
                                        'badge' => 1,
                                        'content-available' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ];

                    try {
                        $response = $client->post('/v1/projects/' . $credentials['project_id'] . '/messages:send', ['json' => $finalData]);
                        $responseBody = $response->getBody()->getContents();

                        if ($response->getStatusCode() >= 400) {
                            Log::warning('FCM push notification failed.', [
                                'status' => $response->getStatusCode(),
                                'body' => $responseBody,
                                'receiver_count' => 1,
                            ]);
                        }

                        $responses[] = $responseBody;
                    } catch (Throwable $exception) {
                        Log::warning('FCM push notification request exception.', [
                            'message' => $exception->getMessage(),
                            'receiver_count' => 1,
                        ]);

                        $responses[] = [
                            'success' => false,
                            'message' => $exception->getMessage(),
                        ];
                    }
                }
            }
        }

        return $responses;
    }

    private static function firebaseCredentials(): array
    {
        $projectId = config('settings.push_notification.project_id') ?: env('FIREBASE_PROJECT_ID');
        $clientEmail = config('settings.push_notification.client_email') ?: env('FIREBASE_CLIENT_EMAIL');
        $privateKey = config('settings.push_notification.private_key') ?: env('FIREBASE_PRIVATE_KEY');

        if (!empty($privateKey)) {
            $privateKey = str_replace('\\n', "\n", $privateKey);
        }

        if (empty($projectId) || empty($clientEmail) || empty($privateKey)) {
            PublicException::Error('PUSH_NOTIFICATION_CREDENTIALS');
        }

        return [
            'project_id' => $projectId,
            'client_email' => $clientEmail,
            'private_key' => $privateKey,
        ];
    }

    private static function firebaseAccessToken(array $credentials): string
    {
        $cacheKey = 'firebase_access_token_' . sha1($credentials['client_email']);

        return Cache::remember($cacheKey, now()->addMinutes(55), function () use ($credentials) {
            $client = new Client(['http_errors' => false]);
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => self::firebaseJwt($credentials),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() >= 400 || empty($body['access_token'])) {
                Log::warning('FCM access token request failed.', [
                    'status' => $response->getStatusCode(),
                    'body' => $body,
                ]);
                PublicException::Error('PUSH_NOTIFICATION_CREDENTIALS');
            }

            return $body['access_token'];
        });
    }

    private static function firebaseJwt(array $credentials): string
    {
        $now = time();
        $header = self::base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));
        $claim = self::base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));
        $unsignedJwt = $header . '.' . $claim;

        $signed = openssl_sign($unsignedJwt, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        if (!$signed) {
            PublicException::Error('PUSH_NOTIFICATION_CREDENTIALS');
        }

        return $unsignedJwt . '.' . self::base64UrlEncode($signature);
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function stringifyData(array $data): array
    {
        $stringData = [];

        foreach ($data as $key => $value) {
            $stringData[$key] = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        }

        return $stringData;
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
              //  dd($users);
                if (isset($users[$receiverId])) {

                    $notificationObject = new Notification();

                    $notificationObject->send_by = $sendby ?? (Auth::check() ? Auth::id() : 0);
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
