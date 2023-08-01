<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // SMTP Credential
        $smtp = [
            'email' => 'office3.cepoch@gmail.com',
            'password' => 'qoomeqhfdaeohxyd',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'from_address' => 'office3.cepoch@gmail.com',
            'from_name' => APP_NAME,
        ];

        $jsonData = json_encode($smtp);
        $settingObj = Setting::where('key', 'SMTP')->first();
        if (!$settingObj) {
            $settingObj = new Setting;
            $settingObj->key = 'SMTP';
            $settingObj->description = 'SMTP setting is using to setup the mail configuration';
        }
        $settingObj->value = $jsonData;
        $settingObj->save();

        // Stripe Credential
        $stripe = [
            'public_key' => 'pk_test_WwE3m17EbrAN4iPdygZyZAFA',
            'secret_key' => 'sk_test_hoq7NJuUIv3gE0Muoy3hZ0Qs',
        ];

        $jsonData = json_encode($stripe);
        $settingObj = Setting::where('key', 'STRIPE')->first();
        if (!$settingObj) {
            $settingObj = new Setting;
            $settingObj->key = 'stripe';
            $settingObj->description = 'Stripe setting is using to setup the payment gateway configuration';
        }
        $settingObj->value = $jsonData;
        $settingObj->save();

        // App Details
        $app = [
            'app_name' => APP_NAME,
            'rate_on_apple_store' => "https://www.google.com/",
            'rate_on_google_store' => "https://www.google.com/",
            'terms_conditions' => "https://www.google.com/",
            'privacy_policy' => "https://www.google.com/",
            'search_distance_limit' => "50",
            'instant_slot_notification' => "30",
        ];

        $jsonData = json_encode($app);
        $settingObj = Setting::where('key', 'APP')->first();

        if (!$settingObj) {
            $settingObj = new Setting;
            $settingObj->key = 'APP';
            $settingObj->description = 'APP setting is using to setup the Application Details';
        }

        $settingObj->value = $jsonData;
        $settingObj->save();

        // Search Distance Limit
        $distance = [
            'search_distance_limit' => "50"
        ];

        $jsonData = json_encode($distance);
        $settingObj = Setting::where('key', 'search_distance_limit')->first();
        if (!$settingObj) {
            $settingObj = new Setting;
            $settingObj->key = 'search_distance_limit';
            $settingObj->description = 'APP setting is using to setup the Application Search distance limit';
        }
        $settingObj->value = $jsonData;
        $settingObj->save();
    }
}
