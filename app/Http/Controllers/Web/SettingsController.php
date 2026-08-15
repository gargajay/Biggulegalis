<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Settings', 'page_icon' => 'fa-cog'];
        $settingsObject = Setting::get()->toArray();
        $data['settings'] = array_combine(array_column($settingsObject, 'key'), $settingsObject);

        return view('web.settings.index', $data);
    }


    public function saveSettings(Request $request, $settingName)
    {
        $data = [];
        switch ($settingName) {
            case 'SMTP':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'SMTP setting is used to set up the mail configuration',
                    'value' => json_encode([
                        'email' => $request->email,
                        'password' => $request->password,
                        'host' => $request->host,
                        'port' => $request->port,
                        'from_address' => $request->from_address,
                        'from_name' => $request->from_name,
                    ])
                ];
                break;

            case 'APP':

                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'APP setting is used to show app information',
                    'value' => json_encode([
                        'app_name' => $request->app_name,
                        'app_icon' => Helper::FileUpload('app_icon', APP_IMAGE_INFO) ?? $request->app_icon_old,
                        'app_color' => $request->app_color,
                        'sidebar_color' => $request->sidebar_color,
                        'otp_signup' => $request->otp_signup,
                        'otp_login' => $request->otp_login,
                        'otp_forgot' => $request->otp_forgot,
                        'rate_on_apple_store' => $request->rate_on_apple_store,
                        'rate_on_google_store' => $request->rate_on_google_store,
                        'terms_conditions' => $request->terms_conditions,
                        'privacy_policy' => $request->privacy_policy,
                        'copyright' => $request->copyright,
                        'tutorial' => $request->tutorial,
                        'help' => $request->help,
                        'about_us' => $request->about_us,
                        'default_radius' => $request->default_radius,
                    ])
                ];
                break;

            case 'PUSH_NOTIFICATION_SERVER_KEY':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'PUSH_NOTIFICATION_SERVER_KEY setting is used to set up the push notification configuration',
                    'value' => json_encode([
                        'push_notification_server_key' => $request->push_notification_server_key,
                        'firebase_project_id' => $request->firebase_project_id,
                        'firebase_client_email' => $request->firebase_client_email,
                        'firebase_private_key' => $request->firebase_private_key,
                    ])
                ];
                break;


            case 'TWILIO':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'TWILIO setting is using to setup the sms configuration',
                    'value' => json_encode([
                        'twilio_sid' => $request->twilio_sid,
                        'twilio_auth_token' => $request->twilio_auth_token,
                        'twilio_number' => $request->twilio_number,
                    ])
                ];
                break;


            case 'MAP_SETTINGS':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'Map settings',
                    'value' => json_encode([
                        'line_color' => $request->line_color,
                        'area_color' => $request->area_color,
                        'line_width' => $request->line_width,
                        'polygon_transparency' => $request->polygon_transparency,
                    ])
                ];
                break;


            case 'S3_BUCKET':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'S3 Bucket Settings',
                    'value' => json_encode([
                        'aws_access_key_id' => $request->aws_access_key_id,
                        'aws_secret_access_key' => $request->aws_secret_access_key,
                        'aws_default_region' => $request->aws_default_region,
                        'aws_bucket' => $request->aws_bucket,
                        'aws_url' => $request->aws_url,
                    ])
                ];
                break;

            case 'STRIPE':
                // Use an array to define the data to update or insert
                $data = [
                    'description' => 'Stripe Settings',
                    'value' => json_encode([
                        'secret_key' => $request->secret_key,
                        'public_key' => $request->public_key,
                    ])
                ];
                break;

            default:
                PublicException::Error('INVALID_REQUEST');
        }

        if ($data) {
            $setting = Setting::updateOrCreate(['key' => $settingName], $data);

            // Check if the setting was not saved
            PublicException::NotSave($setting);

            // Return a success response with the saved data
            return Helper::SuccessReturn($setting, 'SAVE_SETTINGS');
        }
    }

    public function testPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => STATUS_GENERAL_ERROR,
                'message' => $validator->errors()->first(),
                'data' => null,
            ], STATUS_OK);
        }

        try {
            $user = Auth::user();
            $user->device_token = $request->device_token;
            $user->push_notification = '1';
            PublicException::NotSave($user->save());

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
                    'firebase_response' => $response,
                ],
            ], STATUS_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'status' => STATUS_GENERAL_ERROR,
                'message' => $exception->getMessage(),
                'data' => null,
            ], STATUS_OK);
        }
    }
}
