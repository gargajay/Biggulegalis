<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\BlockedUser;
use Aws\Credentials\Credentials;
use Aws\Sts\StsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct(Request $request)
    {
        // make email lower case in request
        updateRequestValue('email', strtolower($request->email));
    }

    public function updateProfile(Request $request)
    {
        // validate rules for input
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'first_name' => ['string', 'max:255', 'nullable'],
            'last_name' => ['nullable', 'string', 'max:255'],
            //'email' => ['required', 'email:strict', 'iunique:users,email,user_type,' . USER_TYPE['USER'] . ',' . Auth::id(), 'max:255'],
            'phone' => ['nullable', 'iunique:users,phone,user_type,' . USER_TYPE['USER'] . ',' . Auth::id(), 'max:255'],
            // 'country_code' => ['required_with:phone', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif'],
            'social_image_url' => ['nullable', 'string', 'max:255', 'url'],
            'date_of_birth' => ['nullable','string'],
            'biography' => ['nullable', 'max:255'],
            'gender' => ['nullable', 'in:' . implode(',', GENDER)]
        ];
          

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();


        $userObject = User::find(Auth::id());

        // set the object properties with the input data
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

        // $userObject->is_profile_completed = PROFILE_COMPLETE['YES'];



        // update address
        $addressObject = Address::find($userObject->address_id) ?? new Address;
        $addressObject->type = ADDRESS_TYPE['USER_ADDRESS'];

        // set the object properties with the input data
        $addressObject = Helper::UpdateObjectIfKeyNotEmpty($addressObject, [
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'country',
            'zip',
            'latitude',
            'longitude',
        ]);

        // $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);

        // if data not save show error
        PublicException::NotSave($addressObject->save());

        $userObject->address_id = $addressObject->id;
        // if data not save show error
        PublicException::NotSave($userObject->save());

        $newImagePath = Helper::FileUpload('image', USER_IMAGE_INFO);
        if ($newImagePath) {
            $userObject->image = $newImagePath;
            PublicException::NotSave($userObject->save());
        }

        return Helper::SuccessReturn($userObject->load(User::$customRelations['Update'], 'goal'), 'PROFILE_UPDATED');
    }


    public function getProfile(Request $request)
    {
        $userId = $request->user_id ?? Auth::id();
        $userObject = User::where('id', )->first();
        $userObject->makeVisible(['date_of_birth', 'biography', 'gender', 'is_profile_completed', 'push_notification', 'language']);

        return Helper::SuccessReturn($userObject, 'PROFILE_FETCHED');
    }


    /**
     * Generates a security token for S3 bucket using AWS STS
     *
     * @return array An array containing the security token and its expiration date
     */
    function generateS3SecurityToken()
    {
        // Create AWS credentials object with key and secret
        $credentials = new Credentials(
            config('filesystems.disks.s3.key'),
            config('filesystems.disks.s3.secret')
        );

        // Set STS client options
        $stsOptions = [
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => $credentials,
        ];

        // Create STS client with the options
        $stsClient = new StsClient($stsOptions);

        // Get session token from STS client
        $result = $stsClient->getSessionToken();

        // Return success response with security token and its expiration date
        return Helper::SuccessReturn($result['Credentials'], 'S3_SECURITY_TOKEN');
    }
    public function editAddress(Request $request)
    {
        $rules = [
            'latitude' => ['required', 'nullable', 'latitude'],
            'longitude' => ['required', 'nullable', 'longitude'],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        $userAddressObject = User::find(Auth::id());

        // update address
        $editaddressObject = Address::find($userAddressObject->address_id);
        $editaddressObject->type = ADDRESS_TYPE['USER_ADDRESS'];

        // set the object properties with the input data
        $editaddressObject = Helper::UpdateObjectIfKeyNotEmpty($editaddressObject, [
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'country',
            'zip',
            'latitude',
            'longitude',
        ]);

        $editaddressObject = Helper::MakeGeolocation($editaddressObject, $request->longitude, $request->latitude);

        // if data not save show error
        PublicException::NotSave($editaddressObject->save());

        return Helper::SuccessReturn($editaddressObject, 'ADDRESS_UPDATED');

    }
}
