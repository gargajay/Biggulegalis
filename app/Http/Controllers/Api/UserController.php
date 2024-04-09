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
use App\Models\Committee;
use App\Models\GroupRole;
use App\Models\Invitation;
use App\Models\OldMember;
use App\Models\UserAssociation;
use Aws\Credentials\Credentials;
use Aws\Sts\StsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            // 'email' => ['email:strict', 'iunique:users,email,user_type,' . USER_TYPE['USER'] . ',' . Auth::id(), 'max:255'],
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

        $user_id = $request->user_id ?? Auth::id();

        $userObject = User::find($user_id);

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

        $roles = [];

        // user associations

        if (!empty($request->is_role_info)) {

            $asso = Association::where('id', $request->association_id)->first();
            $userAssociation =  UserAssociation::where('user_id', $userObject->id)->first();


            if ($asso->id == 2) {
                if(!empty($userAssociation)){
                    $userAssociation->delete();
                }
                
                return Helper::SuccessReturn($userObject->load(User::$customRelations['Update'], 'goal'), 'PROFILE_UPDATED');
            }
            // 3 close permissiom
            if ($asso->permission_type == 3) {
                PublicException::Error('You cannot directly join this association. Please connect with the president of the association for an invitation.');
            }

            if (empty($userAssociation)) {
                $new = true;
                $userAssociation =  new UserAssociation();
            }

            $userAssociation->association_id = $request->association_id;
            $userAssociation->user_id = $userObject->id;
            PublicException::NotSave($userAssociation->save());

            $userAssociation = UserAssociation::find($userAssociation->id);

            if (!empty($request->roles)) {
                $roles = storeJsonArray($request->roles);
                $userAssociation->roles = storeJsonArray($request->roles);
            } else {
                if ($new) {
                    $userAssociation->roles = [8];
                }
            }
            if (!empty($request->roles)) {
                $exit =  UserAssociation::checkPresentExitInAssocation($request->association_id, storeJsonArray($request->roles),$user_id);
                if ($exit) {
                    PublicException::Error($exit);
                }
            }

            $userAssociation =   UserAssociation::handleRoles($userAssociation);
            $userAssociation->user_id = $userObject->id;
            
            PublicException::NotSave($userAssociation->save());
            // send invitation  
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


            
            $members = UserAssociation::where(function ($query) {
                $query->orWhereJsonContains('roles', 5)
                    ->orWhereJsonContains('roles', 6)
                    ->orWhereJsonContains('roles', 4)
                    ->orWhereJsonContains('roles', 7);
            })->where('status', 1)
                ->where('association_id', $request->association_id)
                ->pluck('user_id')
                ->unique() // This will ensure uniqueness
                ->toArray();

                Log::info("memebersqss".json_encode($members));
          

            foreach ($members as $member) {
                $checkUser = User::where('id', $member)->first();
                if (!empty($checkUser)) {
                    $notificationData = [[
                        'receiver_id' => $member,
                        'title' => ['New Member Joined Assoication'],
                        'body' => ['JOINED_MEMBER'],
                        'type' => 'NEW_MEMBER',
                        'app_notification_data' => $userData,
                        'model_id' => $userObject->id,
                        'model_name' => get_class($checkUser),
                    ]];
                    PushNotification::Notification($notificationData, true, true, $userObject->id);
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
        if($request->id !='null' && !empty($request->id)){
            Log::info('dd');
            $userId = $request->id;
        }else{
            Log::info('pp');

            $userId = auth('api')->id();

        }
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


        $userAsso =   UserAssociation::where('user_id', $request->user_id)->where('status',1)->first();

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


    public function getCommiteMembers(Request $request)
    {
        $userIds =  UserAssociation::where(function ($query) {
            // $query->orWhereJsonContains('roles', 4)
            //     ->orWhereJsonContains('roles', 5)
            //     ->orWhereJsonContains('roles', 6)
            //     ->orWhereJsonContains('roles', 7);
        })->where('status',1)
            ->pluck('user_id')->toArray();

        $office =   User::with('userAssociation', 'addresses')->whereIn('id', $userIds)->latest()->get();

        return Helper::SuccessReturn($office, 'GROUP_ROLE_DATA_FETCH');
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

        $userAssociation =  UserAssociation::where('user_id', $userObject->id)->where('status',1)->first();

        if (empty($userAssociation)) {
            $new = true;
            $userAssociation =  new UserAssociation();
        }

        // staff

        $userAssociation->association_id = $request->association_id;
        $userAssociation->user_id = $userObject->id;
        PublicException::NotSave($userAssociation->save());

        $userAssociation->roles = json_decode("[9]");

        PublicException::NotSave($userAssociation->save());


        //   dd($userAssociation);

        $userObject = User::find($userObject->id);

        // generate an access token for the user

        return Helper::SuccessReturn($userObject->load(User::$customRelations['Update']), 'Staff added successfully');
    }






    public function addCommiteMembers(Request $request)
    {
        // Validation rules
        $rules = [
            'members' => ['required'],
            'association_id' => ['required', 'exists:associations,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        // Find or create the committee based on association_id
        $committee = Committee::where('association_id', $request->association_id)->first();

        // If committee doesn't exist, create a new one
        $committee = $committee ?? new Committee();

        // Update committee object with non-empty request data

        // Get existing members and new members from the request
        $committee->association_id = $request->association_id;
        PublicException::NotSave($committee->save());

        $existingMembers = $committee->members ?? [];
        $newMembers = $request->members ? explode(',', $request->members) : [];

        // Merge and remove duplicates from the combined array
        $uniqueMembers = array_unique(array_merge($existingMembers, $newMembers));

        // Update the committee members


        // Update the committee members
        $committee->members =  array_merge($uniqueMembers,[]);

        if(!empty($request->member_id)){
           $user = User::find($request->member_id);
           if(!empty($user)){
            $user->last_name =$request->members;
            $user->save();
           }
        }



        // Save the committee
        PublicException::NotSave($committee->save());

        return Helper::SuccessReturn($committee, 'Committee updated');
    }

    public function deleteCommiteMember(Request $request)
    {
        // Validation rules
        $rules = [
            'member_id' => ['required'],
            'association_id' => ['required', 'exists:associations,id']
        ];
    
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
    
        // Find the committee based on association_id
        $committee = Committee::where('association_id', $request->association_id)->first();
    
        // Check if the committee exists
    
        // Get existing members
        $existingMembers = $committee->members ?? [];
    
        // Remove the specified member from the array
        $updatedMembers = array_values(array_diff($existingMembers, [$request->member_id]));
    
        // Update the committee members
        $committee->members = $updatedMembers;
    
        // Save the committee
        PublicException::NotSave($committee->save());
    
        return Helper::SuccessReturn($committee, 'Member deleted from committee');
    }
    



    public function deleteStaff(Request $request)
    {
        $rules = [
            'association_id' => ['nullable', 'iexists:associations,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        if(!empty($request->staff_id)){
            User::where(['id' => $request->staff_id])->first()->delete();

        }

        if(!empty($request->member_id) && !empty($request->association_id)){
            OldMember::where(['id' => $request->member_id,'association_id'=>$request->association_id])->first()->delete();
        }
        return Helper::SuccessReturn(null, 'STAFF_DELETED');
    }

    public function removeFromAssociation(Request $request)
    {
        $rules = [
            'member_id' => ['required', 'iexists:users,id'],
            'association_id' => ['required', 'iexists:associations,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $userAssociation =  UserAssociation::where('association_id', $request->association_id)->where('user_id', $request->member_id)->where('status',1)->first();
        if (!empty($userAssociation)) {
            $userAssociation->delete();
            $msg = 'STAFF_DELETED';
        } else {
            $msg = 'NOT_FOUND';
        }
        return Helper::SuccessReturn(null, $msg);
    }





    public function oldMember(Request $request)
    {

        $rules = [
            'association_id' => ['required'],
            'phone_no' => ['nullable'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png,jpeg,gif'],
            'year' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable'],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        $oldMember = $request->id ? OldMember::find($request->id) : new OldMember();
        // if (!empty($request->roles)) {
        //     $oldMember->roles = storeJsonArray($request->roles);
        // }

        $oldMember = Helper::UpdateObjectIfKeyNotEmpty($oldMember, [
            'full_name',
            'image',
            'year',
            'association_id',
        ]);



        $oldMember->phone_no = $request->phone;
        $oldMember->enrolment_number = $request->gender;

        PublicException::NotSave($oldMember->save());

        $oldMember = Helper::UpdateObjectIfKeyNotEmpty($oldMember, [
            'roles',
        ]);

        $newImagePath = Helper::FileUpload('image', USER_IMAGE_INFO);
        if ($newImagePath) {
            $oldMember->image = $newImagePath;
            PublicException::NotSave($oldMember->save());
        }

        PublicException::NotSave($oldMember->save());


        return Helper::SuccessReturn($oldMember, 'Old member added successfully');
    }
}
