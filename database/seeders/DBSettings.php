<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DBSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::firstOrCreate(
            ['key' => 'SMTP'],
            [
                'description' => 'SMTP setting is using to setup the mail configuration',
                'value' => json_encode([
                    'email'=>'backendtest711@gmail.com',
                    'password'=>'hssbwugotobuqrmu',
                    'host'=>'smtp.gmail.com',
                    'port'=>'587',
                    'from_address'=>'strengthenapp@gmail.com',
                    'from_name'=>'Strengthen',
                ])
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'TWILIO'],
            [
                'description' => 'TWILIO setting is using to setup the sms configuration',
                'value' => json_encode([
                    'twilio_sid'=>'ACa7699a6ff7c54aab578eded33b4aad16',
                    'twilio_auth_token'=>'7aa718bdb8a0514e005ecd252c384523',
                    'twilio_number'=>'+16067751274',
                ])
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'STRIPE'],
            [
                'description' => 'STRIPE setting is using to setup the payment configuration',
                'value' => json_encode([
                    'secret_key'=>'',
                    'public_key'=>'',
                ])
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'PUSH_NOTIFICATION_SERVER_KEY'],
            [
                'description' => 'PUSH_NOTIFICATION_SERVER_KEY setting is using to setup the push notification configuration',
                'value' => json_encode([
                    'push_notification_server_key'=>'',
                ])
            ]
        );


        Setting::firstOrCreate(
            ['key' => 'DEBUG_MODE'],
            [
                'description' => 'DEBUG_MODE setting is using to show hide error',
                'value' => json_encode([
                    'debug_mode'=>true,
                ])
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'APP'],
            [
                'description' => 'APP setting is using to show app information',
                'value' => json_encode([
                    'app_name' => APP_NAME,
                    'rate_on_apple_store' => "https://www.google.com/",
                    'rate_on_google_store' => "https://www.google.com/",
                    'terms_conditions' => "https://www.google.com/",
                    'privacy_policy' => "https://www.google.com/",
                ])
            ]
        );

    }
}
