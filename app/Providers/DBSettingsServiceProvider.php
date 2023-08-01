<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DBSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            // check db configuration setup or not
            DB::connection()->getPdo();

            if (Schema::hasTable('settings')) {
                //(key <> 'APP') is used to get APP key row in last because in APP icon use s3_bucket url so it create issue
                $settingsObject = Setting::orderBy(DB::raw("(key <> 'APP')"), 'desc')->orderBy('id')->get();
                foreach ($settingsObject as $setting) {
                    $key = strtoupper($setting->key);
                    $value = $setting->value;
                    switch ($key) {
                        case 'SMTP': {
                                config([
                                    'mail.mailers.smtp.host' => isset($value['host']) ? $value['host'] : '',
                                    'mail.mailers.smtp.port' => $value['port'],
                                    'mail.mailers.smtp.username' => $value['email'],
                                    'mail.mailers.smtp.password' => $value['password'],
                                    'mail.from.address' => $value['from_address'],
                                    'mail.from.name' => $value['from_name'],
                                ]);
                                break;
                            }
                        case 'STRIPE': {
                                config([
                                    'settings.stripe.secret_key' => $value['secret_key'],
                                    'settings.stripe.public_key' => $value['public_key'],
                                ]);
                                break;
                            }
                        case 'PUSH_NOTIFICATION_SERVER_KEY': {
                                config([
                                    'settings.push_notification.key' => $value['push_notification_server_key'] ?? NULL,
                                ]);
                                break;
                            }
                        case 'DEBUG_MODE': {
                                config([
                                    'app.debug' => $value['debug_mode'] ?? NULL,
                                ]);
                                break;
                            }
                        case 'TWILIO': {
                                config([
                                    'settings.twilio.twilio_sid' => $value['twilio_sid'],
                                    'settings.twilio.twilio_auth_token' => $value['twilio_auth_token'],
                                    'settings.twilio.twilio_number' => $value['twilio_number'],
                                ]);
                                break;
                            }
                        case 'APP': {
                                config([
                                    'app.name' => $value['app_name'] ?? NULL,
                                    'app.settings' => $value ?? [],
                                ]);
                                break;
                            }
                        case 'S3_BUCKET': {
                                config([
                                    'filesystems.disks.s3.key' => $value['aws_access_key_id'],
                                    'filesystems.disks.s3.secret' => $value['aws_secret_access_key'],
                                    'filesystems.disks.s3.region' => $value['aws_default_region'],
                                    'filesystems.disks.s3.bucket' => $value['aws_bucket'],
                                    'filesystems.disks.s3.url' => $value['aws_url'],
                                ]);
                                break;
                            }
                    }
                }
            }
        } catch (\Exception $e) {
            //nothing do here skip this exception
        }
    }
}
