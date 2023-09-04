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
use App\Models\Link;
use App\Models\Post;
use App\Models\StateBarCouncil;
use App\Models\Tehsil;
use App\Models\User;
use App\Models\UserAssociation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return Helper::SuccessReturn($members,'MEMBERS_FETCH');
    }

    public function getCountries(Request $request)
    {
        $data = Association::where(['parent_id'=>0])->get();
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

        $data = Association::where('parent_id', $request->country_id)->with('country')->get();
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
        
        $data = Association::latest();

        if(!empty($request->state_id)){
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

        $data = Association::latest();

        if(!empty($request->district_id)){
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
        $userIds =   UserAssociation::latest();

        if(!empty($request->association_id)){
            $userIds =  $userIds->where('association_id',$request->association_id);
        }
         $userIds = $userIds->pluck('user_id');
        $users =  User::whereIn('id',$userIds);
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

        $userIds =   UserAssociation::where('association_id',$association->id)->pluck('user_id');
        $members =   User::select('full_name','email','enrolment_number','phone','image','gender','biography')->with('userAssociation')->whereIn('id',$userIds)->latest()->get();
        $gallerys = Gallery::where('association_id', $request->association_id)->latest()->get();

        $president_id =   UserAssociation::where('association_id',$association->id)->where('user_role_id',4)->pluck('user_id');

        $president = User::select('full_name','email','enrolment_number','phone','image','gender','biography')->whereIn('id',$president_id)->with('userAssociation')->latest()->first();

        $links = Link::where('association_id', $request->association_id)->latest()->get();
    

        $associationTabs = [
            [
                'id' =>1,
                'type' => 'overview',
                'information' => $association->description,
                'president'=>$president
            ],
            [
                'id' =>2,
                'type' => 'members',
                'information' => $members
            ],
            [
                'id' =>3,
                'type' => 'gallery',
                'information' => $gallerys
            ],
            [
                'id' =>4,
                'type' => 'links',
                'information' => $links
            ],
        ];

        $association->tabs = $associationTabs;
        

        return Helper::SuccessReturn($association, 'ASSOCIATION_FETCH');

    }
}
