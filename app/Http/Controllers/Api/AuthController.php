<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\PublicException;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

//models
use App\Models\User;
use App\Models\OtpVerification;
use App\Models\Setting;
use App\Models\Address;
use App\Models\Goal;
use App\Models\PasswordReset;
use App\Models\WorkoutHours;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        // make email lower case in request
        updateRequestValue('email', strtolower($request->email));
    }

    /**
     * Signup new user manual
     */
    public function signup(Request $request)
    {
        // validate rules for input
        $rules = [
            'full_name' => ['required', 'string', 'max:255',],
            'first_name' => ['nullable', 'string', 'max:255',],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:strict', 'iunique:users,email,user_type,' . USER_TYPE['USER'], 'max:255'],
            // 'country_code' => ['required_with:phone', 'max:255'],
            'phone' => ['nullable','iunique:users,phone,user_type,' . USER_TYPE['USER'], 'max:255'],
            'password' => ['required', 'strong_password'],
            'device_token' => ['nullable', 'max:255'],
            'device_type' => ['required', 'in:' . implode(',', DEVICE_TYPE)],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif'],
            'timezone' => ['nullable', 'timezone'],
            'latitude' => ['required_with:longitude', 'nullable', 'latitude'],
            'longitude' => ['required_with:latitude', 'nullable', 'longitude'],
            'date_of_birth' => ['nullable', 'string'],
            'biography' => ['nullable', 'string', 'max:255'],
          
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        //add address
        $addressObject = new Address();
        $addressObject->type = ADDRESS_TYPE['USER_ADDRESS'];
        $addressObject->latitude = $request->latitude;
        $addressObject->longitude = $request->longitude;
        // $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);

        // if data not save show error
        PublicException::NotSave($addressObject->save());

        // create a new object add input data
        $userObject = new User;
        $userObject->account_type = ACCOUNT_TYPE['NORMAL'];
        $userObject->user_type = USER_TYPE['USER'];

        $userObject   =    Helper::UpdateObjectIfKeyNotEmpty($userObject,[
            'full_name',
            'first_name',
            'last_name',
            'email',
            'country_code',
            'phone',
            'device_type',
            'device_token',
            'image',
            'social_image_url',
            'timezone',
            'date_of_birth',
            'biography',
            'gender',
            'language',
            'stripe_id',
            'blocked',
            'is_profile_completed',
            'city',
            'annual_income',
            'occupation',
            'company',
            'height',
            'body_shape',
            'ethnicity',
            'hair_color',
            'eye_color',
            'relationship_status',
            'children',
            'smoking',
            'drinking',
            'diet',
            'character',
            'fashion_type',
            'hobby',
            'complete_status',
            'blood_type',
        ]);


        

        // set the object properties with the input data
        // $userObject->first_name = $request->first_name;
        // $userObject->last_name = $request->last_name;
        // $userObject->full_name = $request->full_name;
        // $userObject->email = $request->email;
        // $userObject->phone = $request->phone;
        // $userObject->country_code = $request->country_code;
        $userObject->password = bcrypt($request->password);
        // $userObject->timezone = $request->timezone;
        // $userObject->date_of_birth = $request->date_of_birth;
        // $userObject->biography = $request->biography;
      

        // // set device token and type
        // $userObject->device_token = $request->device_token;
        // $userObject->device_type = $request->device_type;

        // //save address id
        // $userObject->address_id = $addressObject->id;

        // if data not save show error
        PublicException::NotSave($userObject->save());

        $newImagePath = Helper::FileUpload('image', USER_IMAGE_INFO);
        if ($newImagePath) {
            $userObject->image = $newImagePath;
            PublicException::NotSave($userObject->save());
        }

        $userObject = User::find($userObject->id);

        // generate an access token for the user
        $userObject->access_token = $userObject->createToken($userObject->id . ' token')->accessToken;

        return Helper::SuccessReturn($userObject, 'REGISTRATION_SUCCESSFUL');
    }



    /**
     * Login User
     */
    public function login(Request $request)
    {


        // Define rules for input validation
        $rules = [
            'email' => ['required', 'email:strict', 'iexists:users,email,user_type,' . USER_TYPE['USER'], 'max:255'],
            'password' => ['required', 'max:50'],
            'device_token' => ['nullable', 'max:255'],
            'device_type' => ['required', 'nullable', 'in:' . implode(',', DEVICE_TYPE)],
            'timezone' => ['nullable', 'timezone'],
            'latitude' => ['required_with:longitude', 'nullable', 'latitude'],
            'longitude' => ['required_with:latitude', 'nullable', 'longitude'],
        ];

        // Validate the input data based on the defined rules
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        // Verify user credentials using Auth::validate()
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => USER_TYPE['USER']])) {
            PublicException::Error('LOGIN_FAILED');
        }

        // Update device token and type if device token is provided
        $userObject = User::find(Auth::id());

        $userObject = Helper::UpdateObjectIfKeyNotEmpty($userObject, [
            'device_token',
            'device_type',
            'timezone',
        ]);

        // update address
        $addressObject = Address::find($userObject->address_id) ?? new Address;

        // set the object properties with the input data
        $addressObject = Helper::UpdateObjectIfKeyNotEmpty($addressObject, [
            'latitude',
            'longitude',
        ]);

        // $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);

        // if data not save show error
        PublicException::NotSave($addressObject->save());

        $userObject->address_id = $addressObject->id;

        // if data not save show error
        PublicException::NotSave($userObject->save());

        User::logoutFromAllDevices($userObject->id);

        // Retrieve the user object and generate access token
        $userObject->access_token = $userObject->createToken($userObject->id . ' token')->accessToken;

        // Return success response with the user object and message
        return Helper::SuccessReturn($userObject->load(User::$customRelations['Auth'])->append(User::$customAppend['Auth']), 'LOGIN_SUCCESSFUL');
    }



    /**
     * This function handles social login of a user
     */
    public function socialLogin(Request $request)
    {
        $is_new_user = 0;
        // Define the validation rules for the user input data
        $rules = [
            'full_name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:strict', 'max:255'],
            'phone' => ['nullable', 'max:255'],
            'country_code' => ['required_with:phone', 'nullable', 'max:255'],
            'social_type' => ['required', 'in:' . implode(',', array_keys(SOCIAL_PLATFORM))],
            'social_id' => ['required'],
            'device_token' => ['nullable', 'max:255'],
            'device_type' => ['required_with:device_token', 'nullable', 'in:' . implode(',', DEVICE_TYPE)],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'timezone' => ['nullable', 'string'],
            'latitude' => ['required_with:longitude', 'nullable', 'latitude'],
            'longitude' => ['required_with:latitude', 'nullable', 'longitude'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        // Determine the name of the social platform associated with the user's login
        $socialPlatform = SOCIAL_PLATFORM[$request->social_type];
        // Check if a user with the given social ID already exists in the database
        $userObject = User::where($socialPlatform, $request->social_id)->first();

        // If no user exists with the given social ID, check if a user with the given email or phone exists
        if ($request->email) {
            $userObjectEmail = User::where('email', $request->email)->latest()->first();
            if (!IsEmpty($userObjectEmail) && $userObjectEmail->account_type != 3) {
                $userObjectEmail->account_type = ACCOUNT_TYPE['BOTH'];
                $userObjectEmail->save();
            }
        }

        if (!IsEmpty($userObject) && $request->phone && $userObject->account_type != 3) {
            $userObjectPhone = User::where('phone', $request->phone)->where('country_code', $request->country_code)->latest()->first();
            if (!IsEmpty($userObjectPhone)) {
                $userObjectPhone->account_type = ACCOUNT_TYPE['BOTH'];
                $userObjectPhone->save();
            }
        }

        // If no user with the given social ID or email or phone exists, initialize a new user object
        if (IsEmpty($userObject)) {
            $userObject = new User;
            $userObject->account_type = ACCOUNT_TYPE['SOCIAL'];

            if ($userObject->is_profile_completed != PROFILE_COMPLETE['YES']) {
                // Update the user object with the provided data
                $userObject = Helper::UpdateObjectIfKeyExist($userObject,[
                    'full_name',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'country_code',
                    'social_image_url',
                    'user_type',
                ]);
            }

            $is_new_user = 1;

            // Update the user object with the social ID
            $userObject->$socialPlatform = $request->social_id;

            // If a user image was uploaded, update the user object with the new image
            if ($request->hasFile('image')) {
                $userObject->image = Helper::FileUpload('image', USER_IMAGE_INFO);
            }
            // if data not save show error
            PublicException::NotSave($userObject->save());

            // update address
            $addressObject = Address::find($userObject->address_id) ?? new Address;
            $addressObject->type = ADDRESS_TYPE['USER_ADDRESS'];

            // set the object properties with the input data
            $addressObject = Helper::UpdateObjectIfKeyExist($addressObject, [
                'latitude',
                'longitude',
            ]);
            $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);

            // if data not save show error
            PublicException::NotSave($addressObject->save());


            if (empty($userObject->address_id)) {
                $userObject->address_id = $addressObject->id;
                // if data not save show error
                PublicException::NotSave($userObject->save());
            }
        }

        // Update the user object with the provided data
        $userObject = Helper::UpdateObjectIfKeyExist($userObject,[
            'device_token',
            'device_type',
            'timezone',
        ]);
        PublicException::NotSave($userObject->save());

        User::logoutFromAllDevices($userObject->id);
        // Create a new access token for the user and add it to the user object
        $userObject = User::find($userObject->id);
        $userObject->access_token = $userObject->createToken($userObject->id . ' token')->accessToken;
        $userObject->is_new_user = $is_new_user;
        // Return the user data with a success message
        return Helper::SuccessReturn($userObject->load('addresses'), 'LOGIN_SUCCESSFUL');
    }


    /**
     * send otp
     */
    public function sendOTP(Request $request)
    {
        // Define the validation rules for the user input data
        $rules = [
            'email' => ['required_without:phone', 'nullable', 'email:strict', 'iexists:users,email,user_type,' . USER_TYPE['USER'], 'max:255'],
            'phone' => ['required_without:email', 'nullable', 'phone_verify:country_code', 'max:255', 'iexists:users,phone,user_type,' . USER_TYPE['USER'] . ',country_code,' . $request->country_code],
            'country_code' => ['required_with:phone', 'nullable', 'max:255'],
            'otp_purpose' => ['required', 'in:' . implode(',', OTP_PURPOSE)],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        $otp = generateOTP(OTP_LENGHT);
        $successArray = [];

        if ($request->email) {
            // get last otp
            $OtpVerificationObject = OtpVerification::where(['contact' => $request->email, 'mode' => OTP_MODE['EMAIL']])->first();

            if (!IsEmpty($OtpVerificationObject)) {
                $difference = Carbon::now()->diffInSeconds(Carbon::parse($OtpVerificationObject->created_at));
                if ($difference <= OTP_RESEND_TIME) {
                    PublicException::CustomError('OTP_RESEND', ['seconds' => secondsToTimeFormat(OTP_RESEND_TIME - $difference)]);
                }
            }
            //delete previous otp
            OtpVerification::where(['contact' => $request->email, 'mode' => OTP_MODE['EMAIL']])->delete();

            $OtpVerificationObject = new OtpVerification;
            $OtpVerificationObject->contact = $request->email;
            $OtpVerificationObject->mode = OTP_MODE['EMAIL'];
            $OtpVerificationObject->otp = $otp;
            $OtpVerificationObject->purpose = $request->purpose;
            $OtpVerificationObject->token = randomString(20);

            // if the data cannot be saved
            PublicException::NotSave($OtpVerificationObject->save());

            $userObject = User::select('full_name')->where('email', $request->email)->where('user_type', USER_TYPE['USER'])->first();

            $mailData = [
                'to' => $request->email,
                'subject' => 'Forgot One-Time Password',
                'otp' => $otp,
                'userObject' => $userObject,
                'otpExpireTime' => secondsToTimeFormat(OTP_EXPIRE_TIME),
                'view' => 'mail.forgot-password-otp',
            ];

            if (Helper::SendMail($mailData)) {
                $successArray['email_token'] = $OtpVerificationObject->token;
                $successArray['email_resend_time'] = OTP_RESEND_TIME;
            }
        }

        if ($request->phone) {

            $phoneNumber = $request->country_code . $request->phone;

            // get last otp
            $OtpVerificationObject = OtpVerification::where(['contact' => $phoneNumber, 'mode' => OTP_MODE['SMS']])->first();
            if (!IsEmpty($OtpVerificationObject)) {
                $difference = Carbon::now()->diffInSeconds(Carbon::parse($OtpVerificationObject->created_at));
                if ($difference <= OTP_RESEND_TIME) {
                    PublicException::CustomError('OTP_RESEND', ['seconds' => secondsToTimeFormat(OTP_RESEND_TIME - $difference)]);
                }
            }
            //delete previous otp
            OtpVerification::where(['contact' => $phoneNumber, 'mode' => OTP_MODE['SMS']])->delete();

            $OtpVerificationObject = new OtpVerification;
            $OtpVerificationObject->contact = $phoneNumber;
            $OtpVerificationObject->mode = OTP_MODE['SMS'];
            $OtpVerificationObject->otp = $otp;
            $OtpVerificationObject->purpose = $request->purpose;
            $OtpVerificationObject->token = randomString(20);
            if (!$OtpVerificationObject->save()) {
                // if the data cannot be saved
                PublicException::Error('SOMETHING_WENT_WRONG');
            }

            $messageBody = 'Your verification code is ' . $otp . '. This code will expire in ' . secondsToTimeFormat(OTP_EXPIRE_TIME) . '.';
            if (Helper::SendMessage([$phoneNumber], $messageBody)) {
                $successArray['phone_token'] = $OtpVerificationObject->token;
                $successArray['phone_resend_time'] = OTP_RESEND_TIME;
            }
        }

        if ($successArray) {
            return Helper::SuccessReturn($successArray, 'OTP_SEND_SUCCESS');
        }
        PublicException::Error('SOMETHING_WENT_WRONG');
    }


    public function verifyOTP(Request $request)
    {
        // Define the validation rules for the user input data
        $rules = [
            'token' => ['required', 'iexists:otp_verifications,token', 'max:255'],
            'otp' => ['required', 'numeric', 'positive_integer', 'digits:' . OTP_LENGHT],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        // Retrieve OTP verification data from the database based on user input data
        $OtpVerificationObject = OtpVerification::where('token', $request->token)->where('is_otp_verified', false)->first();

        // If the OTP verification object does not exist, throw an error
        PublicException::Empty($OtpVerificationObject, 'INVALID_REQUEST');

        // Check if the OTP has been expired
        if (Carbon::parse($OtpVerificationObject->created_at)->addSeconds(OTP_EXPIRE_TIME)->isBefore(Carbon::now())) {
            PublicException::Error('OTP_EXPIRED');
        }

        // Check if the OTP has been tried multiple times
        if ($OtpVerificationObject->otp_counter >= OTP_RETRY_ATTEMPTS) {
            PublicException::Error('OTP_VERIFY_LIMIT');
        }

        // Check if the OTP matches
        if ($OtpVerificationObject->otp == $request->otp) {
            $OtpVerificationObject->is_otp_verified = true;

            // if data not save show error
            PublicException::NotSave($OtpVerificationObject->save());

            return Helper::SuccessReturn([], 'OTP_VERIFY_SUCCESS');
        }

        // If the OTP does not match, increment the OTP counter and throw an error
        $OtpVerificationObject->otp_counter += 1;

        // if data not save show error
        PublicException::NotSave($OtpVerificationObject->save());
        //save opt counter
        DB::commit();
        PublicException::Error('INVALID_OTP');
    }


    public function resetPasswordUsingOTP(Request $request)
    {
        // Define the validation rules for the user input data
        $rules = [
            'token' => ['required', 'iexists:otp_verifications,token', 'max:255'],
            'password' => ['required', 'strong_password'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        // Find the OTP verification object using token
        $OtpVerificationObject = OtpVerification::where('token', $request->token)->where('is_otp_verified', true)->first();

        // If the OTP verification object does not exist, throw an error
        PublicException::Empty($OtpVerificationObject, 'INVALID_REQUEST');

        // Check if the OTP has been expired
        if (Carbon::parse($OtpVerificationObject->created_at)->addSeconds(RESET_PASSWORD_EXPIRE_TIME)->isBefore(Carbon::now())) {
            PublicException::Error('PASSWORD_RESET_SESSION_EXPIRE');
        }

        // Find the user object for the email or phone number provided
        $userObject = User::where(function ($query) use ($OtpVerificationObject) {
            $query->where('email', $OtpVerificationObject->contact);
            $query->orwhereRaw("CONCAT(country_code,phone) = ?", [$OtpVerificationObject->contact]);
        })->where('user_type', USER_TYPE['USER'])->first();

        // If the user object does not exist, throw an error
        PublicException::Empty($userObject, 'INVALID_REQUEST');

        // Update the user's password with the new one
        $userObject->password = bcrypt($request->password);

        // if data not save show error
        PublicException::NotSave($userObject->save());

        User::logoutFromAllDevices($userObject->id);

        $OtpVerificationObject->delete();
        return Helper::SuccessReturn(null, 'PASSWORD_RESET_SUCCESS');
    }


    public function changePassword(Request $request)
    {
        // Define the validation rules for the user input data
        $rules = [
            'old_password' => ['required', 'min:6', 'max:50'],
            'password' => ['required', 'strong_password'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        $userObject = User::find(Auth::id());

        // Check if the old password provided by the user is valid
        if (!Hash::check($request->old_password, $userObject->password)) {
            PublicException::Error('INVALID_OLD_PASSWORD');
        }

        // Update the user's password with the new one
        $userObject->password = bcrypt($request->password);

        // if data not save show error
        PublicException::NotSave($userObject->save());

        User::logoutFromAllDevices($userObject->id);

        // Save the changes to the database and return a success message if successful
        return Helper::SuccessReturn(null, 'PASSWORD_CHANGE_SUCCESS');
    }


    public function notificationsOnOff(Request $request)
    {
        // Define the validation rules for the user input data
        $rules = [
            'status' => ['required', 'in:1,2' . implode(',', PUSH_NOTIFICATION_USER_SETTING)],//ON=1,OFF=2
            'type' => ['in:1,2' . implode(',', PUSH_NOTIFICATION_USER_SETTING)],//Email=1,Phone=2

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Begin a database transaction
        DB::beginTransaction();

        // Update the user's notification settings with the new value
        $userObject = User::find(Auth::id());

        if ($request->type == 1) { // email notification selected

            $userObject->email_push_notification = $request->status;

            // Send email notification enabled/disabled message
            if ($request->status == 1) {
                $message = 'EMAIL_NOTIFICATION_ENABLED';
            } else {
                $message = 'EMAIL_NOTIFICATION_DISABLED';
            }
        } else if ($request->type == 2) { // phone notification selected

            $userObject->phone_push_notification = $request->status;

            // Send phone notification enabled/disabled message
            if ($request->status == 1) {
                $message = 'PHONE_NOTIFICATION_ENABLED';
            } else {
                $message = 'PHONE_NOTIFICATION_DISABLED';
            }
        } else { // no notification selected
            $userObject->push_notification = $request->status;

            // Send push notification enabled/disabled message
            if ($request->status == 1) {
                $message = 'NOTIFICATION_ENABLED';
            } else {
                $message = 'NOTIFICATION_DISABLED';
            }
        }

        // if data not save show error
        PublicException::NotSave($userObject->save());

        // Save the changes to the database and return a success message if successful
        return Helper::SuccessReturn(['status' => array_flip(PUSH_NOTIFICATION_USER_SETTING)[$request->status]], $message);
    }


    public function logout(Request $request)
    {
        // Retrieve the authenticated user and their access tokens
        Auth::user()->tokens->each(function ($token, $key) {
            // Delete the access token
            $token->delete();
        });

        // Return a success message to indicate that the user has been logged out
        return Helper::SuccessReturn(null, 'LOGOUT_USER');
    }

    public function appSettings(Request $request)
    {
        // Retrieve the application settings from the database
        $appSettings = Setting::where('key', 'APP')->first();

        // Throw an exception if the application settings could not be retrieved
        PublicException::Empty($appSettings, 'NOT_FOUND');

        // Return the application settings in a success response
        return Helper::SuccessReturn($appSettings['value'], 'SETTINGS_FETCHED');
    }


    /**
     * Deletes the user account and logs them out of all devices.
     */
    public function deleteAccount(Request $request)
    {
        // Find the user by their ID.
        $userObject = User::find(Auth::id());

        // Check if the user object is not empty.
        if (!IsEmpty($userObject)) {
            // Delete the user.
            $userObject->delete();

            // Log the user out of all devices.
            User::logoutFromAllDevices($userObject->id);

            // Return a success message.
            return Helper::SuccessReturn([], 'DELETE_USER');
        }

        // Throw an exception if the user cannot be found.
        PublicException::Error('SOMETHING_WENT_WRONG');
    }
    public function getGoal(Request $request)
    {
        $data = [];
        $data['goals'] = Goal::get();
        $data['workout_hours']  = WorkoutHours::get();

        // return a success response with the user data
        return Helper::SuccessReturn($data, 'GOALS_FETCHED');
    }
    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => ['required', 'email:strict', 'iexists:users,email,user_type,' . USER_TYPE['USER'], 'max:255'],
        ];

        // Validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        // Get the user object for the email and user_type
        $userObject = User::where('email', $request->email)->where('user_type', USER_TYPE['USER'])->first();

        // Check if there is a previous password reset object for this email and user_id
        $passwordResetObject = PasswordReset::where(['email' => $request->email, 'user_id' => $userObject->id])->first();

        if (!IsEmpty($passwordResetObject)) {
            // Calculate the difference in seconds between now and the creation time of the previous password reset object
            $difference = Carbon::now()->diffInSeconds(Carbon::parse($passwordResetObject->created_at));

            if ($difference <= FORGOT_EMAIL_RESEND_TIME) {
                // If the time difference is less than or equal to FORGOT_EMAIL_RESEND_TIME, throw an exception
                PublicException::CustomError('FORGOT_EMAIL_RESEND', ['seconds' => secondsToTimeFormat(FORGOT_EMAIL_RESEND_TIME - $difference)]);
            }

            // Delete the previous password reset object
            $passwordResetObject->delete();
        }

        // Generate a new token
        $token = randomString(50, $request->get('email'));
        // Create a new password reset object
        $passwordResetObject = new PasswordReset;
        $passwordResetObject->email = $request->email;
        $passwordResetObject->user_id = $userObject->id;
        $passwordResetObject->token = $token;

        // Save the password reset object to the database
        PublicException::NotSave($passwordResetObject->save());
        // dd($passwordResetObject->id);
        // Create mail data
        $mailData = [
            'to' => $request->email,
            'subject' => 'Reset Password',
            'userObject' => $userObject,
            'resetLink' => route('reset-password', ['token' => $token, 'email' => $request->get('email')]),

            'view' => 'mail.forgot-password-link',
        ];


        // Send the email
        if (Helper::SendMail($mailData)) {
            return Helper::SuccessReturn(null, 'FORGOT_EMAIL_SEND_SUCCESS');
        }

        PublicException::Error('SOMETHING_WENT_WRONG');
    }
}
