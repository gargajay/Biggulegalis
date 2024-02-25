<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Association;
use App\Models\Country;
use App\Models\DistrictBarAssociation;
use App\Models\Gallery;
use App\Models\Announcement;
use App\Models\Committee;
use App\Models\Compliant;
use App\Models\Invitation;
use App\Models\Link;
use App\Models\Post;
use App\Models\Quote;
use App\Models\StateBarCouncil;
use App\Models\Tehsil;
use App\Models\User;
use App\Models\Document;
use App\Models\UserAssociation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OldMember;
use App\Models\OtherPerson;
use App\Helper\PushNotification;


class HomeController extends Controller
{

    public function home()
    {
        $posts = newPagination(Post::with('images', 'user')->latest());
        return Helper::SuccessReturnPagination($posts['data'], $posts['totalPages'], $posts['nextPageUrl'], 'POST_FETCH');
    }

    public function filterMembers(Request $request)
    {
        $members = User::with('addresses')->where('id', '!=', Auth::id())->where('user_type', USER_TYPE['USER'])
            ->select('id', 'full_name', 'image', 'address_id');
        $latitude = $request->latitude ?? null;
        $longitude = $request->longitude ?? null;
        if ($latitude && $longitude) {
            $distance = $request->distance ?? 50;
            $distanceMultiplier = $distance * 1609; // Default 80.46 KMS, 50 Miles, 1 mile = 1.609 Kms
            $addressQuery = Address::select('addresses.*')
                ->selectRaw('*, addresses.created_at AS created_time, addresses.updated_at AS updated_time')
                ->selectRaw('ST_Distance(addresses.geolocation, ST_MakePoint(?,?)::geography) AS distance', [$longitude, $latitude])
                ->whereRaw("ST_Distance(addresses.geolocation, ST_MakePoint(?, ?)::geography) < ?", [$longitude, $latitude, $distanceMultiplier * 1000])
                ->where('type', ADDRESS_TYPE['USER_ADDRESS']);

            $addressIds = $addressQuery->pluck('id')->toArray();
            $members->whereIn('address_id', $addressIds);
        }
        if (!empty($request->fitness_level)) {
            $members->where('fitness_level', $request->fitness_level);
        }
        if (!empty($request->goal_id)) {
            $members->where('goal_id', $request->goal_id);
        }
        if (!empty($request->workout_hours_id)) {
            $members->where('workout_hours_id', $request->workout_hours_id);
        }
        if (!empty($request->search)) {
            $members->where('full_name', 'LIKE', '%' . $request->search . '%');
        }
        $members = newPagination($members->latest());
        return Helper::SuccessReturn($members, 'MEMBERS_FETCH');
    }

    public function getCountries(Request $request)
    {
        $data = Association::where(['parent_id' => 0])->get();
        return Helper::SuccessReturn($data, 'COUNTRY_DATA_FETCH');
    }

    public function getState(Request $request)
    {
        // validate rules for item input
        $rules = [

            // 'country_id' => ['required'],
        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        $country_id = $request->country_id ?? 1;
        $data = Association::where('association_type', '2')->where('parent_id', $country_id)->with('country')->get();
        return Helper::SuccessReturn($data, 'STATE_DATA_FETCH');
        PublicException::Error('SOMETHING_WENT_WRONG');
    }

    public function getDistrict(Request $request)
    {
        // validate rules for item input
        $rules = [

            // 'state_id' => ['required'],
        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        $data = Association::where('association_type', '3')->latest();

        if (!empty($request->state_id)) {
            $data = $data->where('parent_id', $request->state_id);
        }
        $data = $data->with('stateBarCouncil')->get();
        return Helper::SuccessReturn($data, 'DISTRICT_DATA_FETCH');
        PublicException::Error('SOMETHING_WENT_WRONG');
    }

    public function getTehsil(Request $request)
    {
        // validate rules for item input
        $rules = [

            // 'district_id' => ['required'],
        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        $data = Association::where('association_type', '4')->latest();

        if (!empty($request->district_id)) {
            $data = $data->where('parent_id', $request->district_id);
        }
        $data = $data->with('districtBarAssociation')->get();

        return Helper::SuccessReturn($data, 'TEHSIL_DATA_FETCH');
    }


    public function getAllMembers(Request $request)
    {
        $rules = [
            // 'association_id' => ['required'],
        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);
        $userIds =   UserAssociation::where(function ($query) {
            $query->orWhereJsonContains('roles', 9);
        })->pluck('user_id')->toArray();

         $search =  $request->search;

        // // if(!empty($request->association_id)){
        // //     $userIds =  $userIds->where('association_id',$request->association_id);
        // // }
        //  $userIds = $userIds->pluck('user_id');

        $users =  User::where('user_type', '!=', 'admin')->whereNotIn('id',$userIds)->with('userAssociation', 'addresses');
        $users = $users->where('full_name', 'like', '%' . $search . '%');
        $data = newPagination($users->latest());

        return Helper::SuccessReturn($data, 'MEMBER_FETCH');
    }


    public function getAssociationDetail(Request $request)
    {
        $rules = [
            'association_id' => ['required'],
        ];
        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        $association = Association::where('id', $request->association_id)->first();

        $userIds =   UserAssociation::where(function ($query) {
            $query->WhereJsonContains('roles', 8);
        })->where('status', 1)
            ->where('association_id', $request->association_id)
            ->pluck('user_id')->toArray();
        $members =   User::with('userAssociation')->whereIn('id', $userIds)->latest()->get();
        $gallerys = Gallery::where('association_id', $request->association_id)->latest()->get();

        //  $president_id =   UserAssociation::where('association_id',$association->id)->where('user_role_id',4)->pluck('user_id');
        $president_id = UserAssociation::where('association_id', $association->id)->where('status', 1)
            ->whereJsonContains('roles', 4)
            ->pluck('user_id');


        $auth_comm_id = UserAssociation::where('association_id', $association->id)->where('status', 1)
            ->whereJsonContains('roles', 3)
            ->pluck('user_id');

        $members_auth =   User::with('userAssociation')->whereIn('id', $auth_comm_id)->latest()->get();

        $note_public_id = UserAssociation::where('association_id', $association->id)->where('status', 1)
            ->whereJsonContains('roles', 2)
            ->pluck('user_id');

        $members_notry =   User::with('userAssociation')->whereIn('id', $note_public_id)->latest()->get();



        $president = User::with('userAssociation')->whereIn('id', $president_id)->latest()->first();

        $links = Link::where('association_id', $request->association_id)->latest()->get();
        $announcements = Announcement::where('association_id', $request->association_id)->latest()->get();
        $quotes = Quote::where('association_id', $request->association_id)->latest()->get();
        $others = OtherPerson::where('association_id', $request->association_id)->latest()->get();

        $compliants = Compliant::where('association_id', $request->association_id)->latest()->get();

        $office =   UserAssociation::where(function ($query) {
            $query->orWhereJsonContains('roles', 5)
                ->orWhereJsonContains('roles', 6)
                ->orWhereJsonContains('roles', 4)
                ->orWhereJsonContains('roles', 7);
        })->where('status', 1)
            ->where('association_id', $request->association_id)
            ->pluck('user_id')->toArray();

        $officeBear =   User::with('userAssociation')->whereIn('id', $office)->latest()->get();


        $committee = Committee::where('association_id',  $request->association_id)->latest()->first();
        $cmembers = [];
        if (!empty($committee)) {
            $committee->members;

            $cmembers =   User::whereIn('id', $committee->members)->with('addresses', 'userAssociation')->latest()->get();
        }
        $oldMembers = OldMember::where('association_id', $request->association_id)->latest()->get();

        $staffids =   UserAssociation::where(function ($query) {
            $query->WhereJsonContains('roles', 9);
            })->where('status', 1)
            ->where('association_id', $request->association_id)
            ->pluck('user_id')->toArray();

            $staff =   User::with('userAssociation')->whereIn('id', $staffids)->latest()->get();



        $associationTabs = [
            [
                'id' => 1,
                'name' => 'overview',
                'type' => 'overview',
                'information' => [],
                'president' => $president,
                'description' => $association->description
            ],
            [
                'id' => 2,
                'name' => 'members',
                'type' => 'members',
                'information' => $members
            ],
            [
                'id' => 3,
                'name' => 'gallery',
                'type' => 'gallery',
                'information' => $gallerys
            ],
            [
                'id' => 4,
                'name' => 'links',
                'type' => 'links',
                'information' => $links
            ],
            [
                'id' => 5,
                'name' => 'Courts',
                'type' => 'quotes',
                'information' => $quotes
            ],
            [
                'id' => 6,
                'name' => 'announcements',
                'type' => 'announcements',
                'information' => $announcements
            ],
            [
                'id' => 7,
                'name' => 'Notary public',
                'type' => 'members',
                'information' =>  $members_notry
            ],
            [
                'id' => 8,
                'name' => 'Oath commissioner',
                'type' => 'members',
                'information' => $members_auth
            ],
            [
                'id' => 9,
                'name' => 'Complaints',
                'type' => 'compliant',
                'information' => $compliants
            ],
            [
                'id' => 10,
                'name' => 'Office bearers',
                'type' => 'officebear',
                'information' => $officeBear,
                'old_member' => $oldMembers,
                'staff' =>$staff
            ],
            [
                'id' => 11,
                'name' => 'Others',
                'type' => 'others',
                'information' => $others,
            ],
           



        ];

        if($association->association_type==1 || $association->association_type==2){
            $associationTabs [] =    [
                'id' => 11,
                'name' => 'disciplinary committee',
                'type' => 'members',
                'information' => $cmembers
            ];
        }

       

        $association->tabs = $associationTabs;


        return Helper::SuccessReturn($association, 'ASSOCIATION_FETCH');
    }


    public function Quote(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'id' => ['nullable', 'integer', 'iexists:quotes,id'],
            'association_id' => ['required', 'integer', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $quote = !empty($id) ? Quote::find($request->id) : new Quote;
        $quote->user_id = Auth::id();
        $quote = Helper::UpdateObjectIfKeyNotEmpty($quote, [
            'name',
            'description',
            'association_id'
        ]);

        $quote->user_id = Auth::id();

        // if data not save show error

        PublicException::NotSave($quote->save());
        // add quote members

        return Helper::SuccessReturn($quote->load('association'), !empty($id) ? 'QUOTE_UPDATED' : 'QUOTE_ADDED');
    }

    public function deleteQuote(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:quotes,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Quote::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'QUOTE_DELETED');
    }

    public function otherPerson(Request $request)
    {
        $rules = [
            'id' => ['nullable'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'association_id' => ['required', 'iexists:associations,id'],
            'work' => ['nullable'],
            'contact_no' => ['nullable']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $otherPerson = OtherPerson::find($request->id) ? OtherPerson::find($request->id) : new OtherPerson();

        // Update other person fields
        $otherPerson = Helper::UpdateObjectIfKeyNotEmpty($otherPerson, [
            'name',
            'description',
            'association_id',
            'work',
            'contect_no'
        ]);

        $otherPerson->user_id = auth::id();

        // Save the updated other person
        PublicException::NotSave($otherPerson->save());

        return Helper::SuccessReturn($otherPerson->load('association'), 'OTHER_PERSON_UPDATED');
    }

    public function deleteOtherPerson(Request $request)
    {
        $rules = [
            'id' => ['required', 'iexists:other_persons,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        OtherPerson::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'OTHER_PERSON_DELETED');
    }

    // annocements

    public function announcement(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'id' => ['nullable', 'integer', 'iexists:announcements,id'],
            'association_id' => ['required', 'integer', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $announcement = !empty($id) ? Announcement::find($request->id) : new Announcement;
        $announcement->user_id = Auth::id();
        $announcement = Helper::UpdateObjectIfKeyNotEmpty($announcement, [
            'name',
            'description',
            'association_id'
        ]);

        $announcement->user_id = Auth::id();

        // if data not save show error

        PublicException::NotSave($announcement->save());

        $members = UserAssociation::where('association_id', $request->association_id)
            ->pluck('user_id')
            ->unique() // This will ensure uniqueness
            ->toArray();

            $userData = [
                'id' => Auth::id(),
                'full_name' => Auth::user()->full_name,
                'image' => Auth::user()->image,
            ];

        foreach ($members as $member) {
            $checkUser = User::where('id', $member)->first();
            if (!empty($checkUser)) {
                $notificationData = [[
                    'receiver_id' => $member,
                    'title' => ['New Announcement'],
                    'body' => ['Announcement'],
                    'type' => 'announcement',
                    'app_notification_data' => $userData,
                    'model_id' =>  $announcement->id,
                    'model_name' => get_class( $announcement),
                ]];
                PushNotification::Notification($notificationData, true, true, $announcement->user_id);
            }
        }
        // add announcement members

        return Helper::SuccessReturn($announcement->load('association'), !empty($id) ? 'ANNOUNCEMENT_UPDATED' : 'ANNOUNCEMENT_ADDED');
    }

    public function deleteAnnouncement(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:announcements,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Announcement::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'ANNOUNCEMENT_DELETED');
    }


    public function compliant(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable'],
            'id' => ['nullable', 'iexists:compliants,id'],
            'association_id' => ['required', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $announcement = !empty($id) ? Compliant::find($request->id) : new Compliant;
        $announcement->user_id = Auth::id();
        $announcement = Helper::UpdateObjectIfKeyNotEmpty($announcement, [
            'name',
            'description',
            'association_id'
        ]);

        $announcement->user_id = Auth::id();

        // if data not save show error

        PublicException::NotSave($announcement->save());
        // add announcement members

        return Helper::SuccessReturn($announcement->load('association'), !empty($id) ? 'compliant_UPDATED' : 'ANNOUNCEMENT_ADDED');
    }


    public function deleteCompliant(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:announcements,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Compliant::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'compliant_DELETED');
    }




    public function sendInvitation(Request $request)
    {
        $rules = [
            'user_id' => ['required', 'integer', 'iexists:users,id'],
            'association_id' => ['required', 'integer', 'iexists:associations,id']

        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);

        $userPermissionArray =  User::getAllPermissions(Auth::id(),true,true);
          if(!in_array(10,$userPermissionArray)){
            return response()->json(['success' => FALSE, 'status' =>400, 'message' => __("message.no_permission")], 200);
          }

          $appnotification = false;

          $invitation = Invitation::where(['user_id'=>$request->user_id,'association_id'=>$request->association_id,'type'=>'from_user'])->first();
     if(empty($invitation)){
        $invitation = new Invitation();
        $appnotification = true;
     }
        $invitation->user_id = $request->user_id;
        $invitation->msg = Auth::user()->full_name . ' sent you request to join in his association';
        $invitation->association_id = $request->association_id;
        PublicException::NotSave($invitation->save());

        $userData = [
            'id' => Auth::id(),
            'full_name' => Auth::user()->full_name,
            'image' => Auth::user()->image,
        ];

        $notificationData = [[
            'receiver_id' => $request->user_id,
            'title' => ['Invitation for join assocation'],
            'body' => ['Invitation'],
            'type' => 'Invitation',
            'app_notification_data' => $userData,
            'model_id' => $request->user_id,
            'model_name' => get_class($invitation),
        ]];

        
        PushNotification::Notification($notificationData, $appnotification, true, Auth::id());

        return Helper::SuccessReturn(null, 'Invitation_sent');
    }

    public function getAllDocument()
    {
        $Data = Document::latest()->get();
        return Helper::SuccessReturn($Data, 'Document list');
    }

    public function buyDocument(Request $request)
    {
        $Data = Document::find($request->document_id);
        $url = url('payment?document_id='). $Data->id."&u_id=". base64_encode(Auth::id());
      $response =  ['url'=>$url];
        return Helper::SuccessReturn($url, 'payment url');

    }

    
}
