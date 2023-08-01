<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Helper\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\User;
use Carbon\carbon;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function getEvent(Request $request)
    {
        $rules = [
            'date' => ['nullable', 'date_format:Y-m-d'],
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $events = Event::with('address', 'members')
            ->where('user_id', Auth::id());
        if (!empty($request->date)) {
            $events->whereDate('start', $request->date);
        }
        $events = newPagination($events->latest());
        return Helper::SuccessReturnPagination($events['data'], $events['totalPages'], $events['nextPageUrl'], 'EVENT_FETCH');
    }

    public function getEventDetails(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:events,id']
        ];
        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $events = Event::with('address', 'members')->where('id', $request->get('id'))->latest()->get();
        return Helper::SuccessReturn($events, 'EVENT_DETAILS');
    }

    public function getMembers(Request $request)
    {
        $name = $request->get('search');
        $members = User::where('id', '!=', Auth::id())->where('user_type', USER_TYPE['USER'])->select('id', 'full_name', 'email', 'image');
        if (isset($name) && !empty($name)) {
            $members->where('full_name', 'LIKE', '%' . $name . '%');
        }
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        if ($latitude && $longitude) {
            $distanceMultiplier = 50 * 1609; // Default 80.46 KMS, 50 Miles, 1 mile = 1.609 Kms
            $addressQuery = Address::select('addresses.*')
                ->selectRaw('*, addresses.created_at AS created_time, addresses.updated_at AS updated_time')
                ->selectRaw('ST_Distance(addresses.geolocation, ST_MakePoint(?,?)::geography) AS distance', [$longitude, $latitude])
                ->whereRaw("ST_Distance(addresses.geolocation, ST_MakePoint(?, ?)::geography) < ?", [$longitude, $latitude, $distanceMultiplier * 1000])
                ->where('type', ADDRESS_TYPE['USER_ADDRESS']);

            $addressIds = $addressQuery->pluck('id')->toArray();
            $members->whereIn('address_id', $addressIds);
        }
        $members = newPagination($members);
        return Helper::SuccessReturnPagination($members['data'], $members['totalPages'], $members['nextPageUrl'], 'MEMBERS_FETCH');
    }

    public function event(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'start' => ['required', 'date_format:Y-m-d H:i:s'],
            'end' => ['required', 'date_format:Y-m-d H:i:s'],
            'image' => ['nullable', 'mimes:jpeg,png,jpg,gif'],
            'members' => ['required', 'string'],
            'latitude' => ['required', 'nullable', 'latitude'],
            'longitude' => ['required', 'nullable', 'longitude'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:255'],
            'id' => ['nullable', 'integer', 'iexists:events,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        $id = $request->id;
        $event = !empty($id) ? Event::find($request->id) : new Event;
        $event->user_id = Auth::id();
        $event = Helper::UpdateObjectIfKeyNotEmpty($event, [
            'title',
            'start',
            'end',
        ]);
        if ($request->has('image')) {
            $event->image = Helper::FileUpload('image', EVENT_IMAGE_INFO);
        }
        // update address
        $addressObject = !empty($id) ? Address::find($event->address_id) : new Address;
        $addressObject->type = ADDRESS_TYPE['EVENT_ADDRESS'];
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
        $addressObject = Helper::MakeGeolocation($addressObject, $request->longitude, $request->latitude);
        // if data not save show error
        PublicException::NotSave($addressObject->save());
        $event->address_id = $addressObject->id;
        PublicException::NotSave($event->save());
        // add event members
        $userData = [
            'id' => Auth::id(),
            'name' => Auth::user()->full_name,
            'image' => Auth::user()->image,
        ];
        $members = $request->members;
        if (!empty($members)) {
            $members = json_decode($members);
            if (!empty($id)) {
                $getEventMembers = EventMember::where(['event_id' => $id])->pluck('user_id')->toArray();
                $deleteMembers = array_diff($getEventMembers, $members);
                if (!empty($deleteMembers)) {
                    EventMember::where(['event_id' => $id])->whereIn('user_id', $deleteMembers)->delete();
                }
                $members = array_diff($members, $getEventMembers);
            }
            foreach ($members as $member) {
                $checkUser = User::where('id', $member)->first();
                if (!empty($checkUser)) {
                    $eventMember = new EventMember;
                    $eventMember->user_id = $member;
                    $eventMember->event_id = $event->id;
                    PublicException::NotSave($eventMember->save());
                    $notificationData = [[
                        'receiver_id' => $members,
                        'title' => ['New Event Created'],
                        'body' => ['EVENT_CREATED' => ['name' => $event->title, 'time' => Carbon::parse($event->start)->format('m-d-Y g:i A')]],
                        'type' => 'event_reminder',
                        'app_notification_data' => $userData,
                        'model_id' => $event->id,
                        'model_name' => get_class($event),
                    ]];
                    PushNotification::Notification($notificationData, true, false, $event->user_id);
                }
            }
        }
        return Helper::SuccessReturn($event->load('address', 'members'), !empty($id) ? 'EVENT_UPDATED' : 'EVENT_ADDED');
    }

    public function deleteEvent(Request $request)
    {
        $rules = [
            'id' => ['required', 'integer', 'iexists:events,id']
        ];

        // Validate the user input data
        PublicException::Validator($request->all(), $rules);
        Event::find($request->id)->delete();
        return Helper::SuccessReturn(null, 'EVENT_DELETED');
    }
}
