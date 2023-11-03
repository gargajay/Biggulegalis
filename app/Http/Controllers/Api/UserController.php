<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\Association;
use App\Models\BlockedUser;
use App\Models\GroupRole;
use App\Models\UserAssociation;
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
        $new = false;
        // validate rules for input
        $rules = [
            'full_name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['string', 'max:255', 'nullable'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['email:strict', 'iunique:users,email,user_type,' . USER_TYPE['USER'] . ',' . Auth::id(), 'max:255'],
            // 'phone' => ['nullable', 'iunique:users,phone,user_type,' . USER_TYPE['USER'] . ',' . Auth::id(), 'max:255'],
            // 'country_code' => ['required_with:phone', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif'],
            'social_image_url' => ['nullable', 'string', 'max:255', 'url'],
            'date_of_birth' => ['nullable', 'string'],
            'biography' => ['nullable', 'max:255'],
            'gender' => ['nullable', 'in:' . implode(',', GENDER)]

        ];

        if (!empty($request->is_role_info)) {
            $rules['user_role'] = ['nullable'];
            $rules['association_id'] = ['nullable', 'integer'];
            $rules['association_type'] = ['nullable', 'integer'];
        }

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();


        $userObject = User::find(Auth::id());

        // set the object properties with the input data
        $userObject   =    Helper::UpdateObjectIfKeyNotEmpty($userObject, [
            'full_name',
            'first_name',
            'last_name',
            'email',
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

        $new = false;

        // user associations

        if (!empty($request->is_role_info)) {

            $asso = Association::where('id',$request->association_id)->first();

            // 3 close permissiom
            if($asso->permission_type==3)
            {
                PublicException::Error('You cannot be directly join this assoication . connect to priesent of association for inviation');
            }

            $userAssociation =  UserAssociation::where('association_id',$request->association_id)->where('user_id', $userObject->id)->first();

            if (empty($userAssociation)) 
            {

                $new = true;
                $userAssociation =  new UserAssociation();
            }

            $userAssociation = Helper::UpdateObjectIfKeyNotEmpty($userAssociation, [
                'user_role_id',
                'association_id',
            ]);

            if (!empty($request->roles)) {
                $userAssociation->roles = storeJsonArray($request->roles);
            } else {
                if ($new) {
                    $userAssociation->roles = ['8'];
                }
            }

            if(!empty($request->roles)){
            $exit =  UserAssociation::checkPresentExitInAssocation($request->association_id,storeJsonArray($request->roles));
              if($exit){
                PublicException::Error($exit);
              }
            }



            $userAssociation->user_id = $userObject->id;
            PublicException::NotSave($userAssociation->save());
            $userData = [
                'id' => Auth::id(),
                'full_name' => Auth::user()->full_name,
                'image' => Auth::user()->image,
            ];
        }

        // $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);
        // if data not save show error
        PublicException::NotSave($addressObject->save());

        $userObject->address_id = $addressObject->id;
        // if data not save show error
        PublicException::NotSave($userObject->save());

        if ($new) {

            $members =   UserAssociation::where('association_id', $request->association_id)->where('user_id', '!=', $userObject->id)->pluck('user_id')->toArray();

            foreach ($members as $member) {
                $checkUser = User::where('id', $member)->first();
                if (!empty($checkUser)) {
                    $notificationData = [[
                        'receiver_id' => $members,
                        'title' => ['New Member Joined Assoication'],
                        'body' => ['JOINED_MEMBER'],
                        'type' => 'NEW_MEMBER',
                        'app_notification_data' => $userData,
                        'model_id' => $userObject->id,
                        'model_name' => get_class($checkUser),
                    ]];
                    PushNotification::Notification($notificationData, true, false, $userObject->id);
                }
            }
        }

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
        $userObject = User::where('id', $userId)->with('userAssociation', 'addresses')->first();
        $userObject->makeVisible(['date_of_birth', 'biography', 'gender', 'is_profile_completed', 'push_notification', 'language']);
        $userObject->all_permissions = User::getAllPermissions($userId);
        $userObject->tabs = $userObject->tabs();

        return Helper::SuccessReturn($userObject, 'PROFILE_FETCHED');
    }

    public function savePermission(Request $request)
    {
        $rules = [
            'user_id' => ['required'],
        ];


        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);
        $userId = $request->user_id;

        $userObject = User::where('id', $userId)->with('userAssociation', 'addresses')->first();
        $userObject->makeVisible(['date_of_birth', 'biography', 'gender', 'is_profile_completed', 'push_notification', 'language']);


        $userAsso =   UserAssociation::where('user_id', $request->user_id)->first();

        //  dd($userAsso);
        if (!empty($userAsso)) {

            $userAsso->permissions = $request->permissions;
            $userAsso->save();
        }

        $userObject->all_permissions = User::getAllPermissions($userId);
        $userObject->tabs = $userObject->tabs();

        return Helper::SuccessReturn($userObject, 'PROFILE_FETCHED');
    }




    public function getGroupRoleList(Request $request)
    {
        $dataList = GroupRole::where('id', '!=', 1)->get();

        return Helper::SuccessReturn($dataList, 'GROUP_ROLE_DATA_FETCH');
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


    public function staff(Request $request)
    {
        $rules = [
            'country_code' => ['required_with:phone', 'max:255'],
            'phone' => ['nullable', 'iunique:users,phone,user_type,' . USER_TYPE['USER'] . ',' . $request->id, 'max:255'],
            'full_name' => ['sometimes', 'string', 'max:255',],
            'first_name' => ['nullable', 'string', 'max:255',],
            'last_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif'],
            'biography' => ['nullable', 'string', 'max:255'],

        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        // create a new object add input data

        $userObject =  User::find($request->id) ? User::find($request->id) : new User;
        $userObject->account_type = ACCOUNT_TYPE['NORMAL'];
        $userObject->user_type = USER_TYPE['USER'];

        $userObject   =    Helper::UpdateObjectIfKeyNotEmpty($userObject, [
            'full_name',
            'first_name',
            'last_name',
            'country_code',
            'phone',
        ]);

        $userObject->parent_id = Auth::id();

        $userObject->password = bcrypt('12345678');

        // if data not save show error
        PublicException::NotSave($userObject->save());

        $newImagePath = Helper::FileUpload('image', USER_IMAGE_INFO);
        if ($newImagePath) {
            $userObject->image = $newImagePath;
            PublicException::NotSave($userObject->save());
        }

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

        PublicException::NotSave($addressObject->save());

        $userObject->address_id = $addressObject->id;
        // if data not save show error
        PublicException::NotSave($userObject->save());

        // user associations

        $userAssociation =  UserAssociation::where('user_id', $userObject->id)->first();

        if (empty($userAssociation)) {
            $new = true;
            $userAssociation =  new UserAssociation();
        }
        // staff
        $userAssociation->user_role_id = 8;
        $userAssociation->association_id = $request->association_id;
        $userAssociation->user_id = $userObject->id;
        PublicException::NotSave($userAssociation->save());

        $userObject = User::find($userObject->id);

        // generate an access token for the user

        return Helper::SuccessReturn($userObject->load(User::$customRelations['Update']), 'Staff added successfully');
    }

    public function deleteStaff(Request $request)
    {
        $rules = [
            'staff_id' => ['required', 'iexists:users,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        User::where(['id' => $request->staff_id, 'parent_id' => Auth::id()])->first()->delete();
        return Helper::SuccessReturn(null, 'STAFF_DELETED');
    }
}
