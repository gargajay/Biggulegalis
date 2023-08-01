<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        // make email lower case in request
        updateRequestValue('email', strtolower($request->email));
    }

    public function login(Request $request)
    {
        if (Auth::id()) {
            return redirect()->route('dashboard');
        }

        if ($request->isMethod('post')) {

            // Define rules for input validation
            $rules = [
                'email' => ['required', 'email:strict', 'iexists:users,email,user_type,' . USER_TYPE['ADMIN'], 'max:255'],
                'password' => ['required', 'min:6', 'max:50'],
            ];
            // Validate the input data based on the defined rules
            PublicException::Validator($request->all(), $rules);

            // Begin database transaction
            DB::beginTransaction();

            // Verify user credentials using Auth::validate()
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => USER_TYPE['ADMIN']])) {
                PublicException::Error('LOGIN_FAILED');
            }

            // Update device token and type if device token is provided
            $userObject = User::find(Auth::id());

            // if data not save show error
            PublicException::NotSave($userObject->save());

            User::logoutFromAllDevices($userObject->id);

            // Return success response with the user object and message
            return Helper::SuccessReturn($userObject, 'LOGIN_SUCCESSFUL');
        } else {
            return view('web.auth.login');
        }
    }


    public function changePassword(Request $request)
    {
        if ($request->isMethod('post')) {

            // Define rules for input validation
            $rules = [
                'old_password' => ['required', 'string', 'min:6', 'max:50', 'current_password'],
                'password' => ['required', 'string', 'strong_password', 'confirmed'],
            ];

            // Validate the input data based on the defined rules
            PublicException::Validator($request->all(), $rules, [], ['password' => 'new password']);

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
            return Helper::SuccessReturn([], 'PASSWORD_CHANGE_SUCCESS');
        } else {
            return view('web.auth.change-password');
        }
    }


    public function updateProfile(Request $request)
    {
        $userObject = User::find(Auth::id());
        if ($request->isMethod('post')) {

            // validate rules for input
            $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email:strict', 'iunique:users,email,user_type,' . USER_TYPE['ADMIN'] . ',' . Auth::id(), 'max:255'],
                'image' => ['nullable', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            ];

            // validate input data using the Validator method of the PublicException class
            PublicException::Validator($request->all(), $rules);

            // Begin database transaction
            DB::beginTransaction();

            // set the object properties with the input data
            $userObject = Helper::UpdateObjectIfKeyExist($userObject, [
                'first_name',
                'last_name',
                'email'
            ]);

            if ($request->hasFile('image')) {
                $userObject->image = Helper::FileUpload('image', USER_IMAGE_INFO);
            }

            // if data not save show error
            PublicException::NotSave($userObject->save());


            return Helper::SuccessReturn($userObject, 'PROFILE_UPDATED');
        } else {
            return view('web.auth.update-profile', ['data' => $userObject]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }


    public function forgotPassword(Request $request)
    {
        // Check if request method is POST
        if ($request->isMethod('post')) {

            // Define the validation rules for the user input data
            $rules = [
                'email' => ['required', 'email:strict', 'iexists:users,email,user_type,' . USER_TYPE['ADMIN'], 'max:255'],
            ];

            // Validate input data using the Validator method of the PublicException class
            PublicException::Validator($request->all(), $rules);

            // Begin database transaction
            DB::beginTransaction();

            // Get the user object for the email and user_type
            $userObject = User::where('email', $request->email)->where('user_type', USER_TYPE['ADMIN'])->first();

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
            $token = randomString(30);

            // Create a new password reset object
            $passwordResetObject = new PasswordReset;
            $passwordResetObject->email = $request->email;
            $passwordResetObject->user_id = $userObject->id;
            $passwordResetObject->token = $token;

            // Save the password reset object to the database
            PublicException::NotSave($passwordResetObject->save());

            // Create mail data
            $mailData = [
                'to' => $request->email,
                'subject' => 'Reset Password',
                'userObject' => $userObject,
                'resetLink' => route('reset-password', ['token' => $token]),
                'view' => 'mail.forgot-password-link',
            ];

            // Send the email
            if (Helper::SendMail($mailData)) {
                return Helper::SuccessReturn([], 'FORGOT_EMAIL_SEND_SUCCESS');
            }

            PublicException::Error('SOMETHING_WENT_WRONG');
        } else {
            // If request method is not POST, return the forgot-password view
            return view('web.auth.forgot-password');
        }
    }


    /**
     * This function is used to reset the password for a user account.
     */
    public function resetPassword(Request $request, $token)
    {
        // Check if request method is POST
        if ($request->isMethod('post')) {

            // Define the validation rules for the user input data
            $rules = [
                'password' => ['required', 'string', 'strong_password', 'confirmed'],
            ];

            // Validate input data using the Validator method of the PublicException class
            PublicException::Validator($request->all(), $rules);

            // Begin database transaction
            DB::beginTransaction();

            // Check token data is available
            $passwordResetObject = PasswordReset::where(['token' => $token])->first();

            // Throw an exception if no password reset object is found
            PublicException::Empty($passwordResetObject, 'INVALID_REQUEST');

            // Check if the link has expired
            if (Carbon::parse($passwordResetObject->created_at)->addSeconds(FORGOT_EMAIL_EXPIRE_TIME)->isBefore(Carbon::now())) {
                // Throw an exception if the link has expired
                PublicException::Error('FORGOT_EMAIL_EXPIRED');
            }

            // Get the user object for the email and user_type
            $userObject = User::where('id', $passwordResetObject->user_id)
                ->where('email', $passwordResetObject->email)
                ->first();

            // Throw an exception if no user object is found
            PublicException::Empty($userObject, 'INVALID_REQUEST');

            // Update the user's password with the new one
            $userObject->password = bcrypt($request->password);

            // Save the updated user object and check for errors
            PublicException::NotSave($userObject->save());

            // Delete the password reset object
            $passwordResetObject->delete();

            // Return a success response
            return Helper::SuccessReturn([], 'PASSWORD_RESET_SUCCESS');
        } else {

            // Check token data is available
            $passwordResetObject = PasswordReset::where(['token' => $token])->first();

            // if object is empty
            if (IsEmpty($passwordResetObject)) {
                PublicException::ErrorWebPage('RESET_LINK_EXPIRED', STATUS_LINK_EXPIRED);
            }

            // Check if the link has expired
            if (Carbon::parse($passwordResetObject->created_at)->addSeconds(FORGOT_EMAIL_EXPIRE_TIME)->isBefore(Carbon::now())) {

                // Delete the password reset object
                $passwordResetObject->delete();

                // Throw an exception if the link has expired
                PublicException::ErrorWebPage('RESET_LINK_EXPIRED', STATUS_LINK_EXPIRED);
            }

            // If request method is not POST, return the reset-password view
            return view('web.auth.reset-password', compact('token'));
        }
    }
}
