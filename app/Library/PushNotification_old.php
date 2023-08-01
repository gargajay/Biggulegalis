<?php

namespace App\Library;

use App\Models\Setting;

class PushNotification
{
    protected static $serverKey = NULL;

    public function __construct()
    {
    }

    public static function send($notificationData = [])
    {
        $serverKey = "AAAAPJ6nUd8:APA91bEHFQlVul37cPeNBV6upACUAJ0ChBBD4OE-3P0VTfuv9WH-CdoeYe5E3ehwMiipPARa_q5lRbputOFHN9UtnTVMhmJUXGU6RMxVjYM2D_aVdSe2UJh1BS7s6HHKkvZnp8UjOqDW";

        // $settingObj = Setting::where('name', 'push_notification_server_key')->first();

        // if ($settingObj) {
        //     $value = $settingObj->value;

        //     if (isset($value['push_notification_server_key'])) {
        //         $serverKey = $value['push_notification_server_key'];
        //     }
        // }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            "registration_ids" => array(
                $notificationData['device_token']
            ),
            "notification" => array(
                "title" => $notificationData['title'] ?? "",
                "body" => $notificationData['message'],
                "sendby" => $notificationData['send_by'],
                "type" => $notificationData['type'],
                "Info"=> $notificationData['Info'],
                "content-available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ),
            "data" => array(
                "title" => $notificationData['title'] ?? "",
                "body" => $notificationData['message'],
                "sendby" => $notificationData['send_by'],
                "type" => $notificationData['type'],
                "Info"=> $notificationData['Info'],
                "content-available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ),
            "priority" => 10
        );

        if (isset($notificationData['metadata']) && !empty($notificationData['metadata'])) {
            $fields['notification']['metadata'] = $notificationData['metadata'];
            $fields['data']['metadata'] = $notificationData['metadata'];
        }

        //print_pre($fields);
        $fields = json_encode($fields);
        $headers = array(
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        // print_pre($result);

        return $result;
    }
}
