<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        $notificationObject = Notification::where('received_by', Auth::id())->where('read', false)->orderBy('id', 'desc');
        $notificationObject = newPagination($notificationObject);
        return Helper::SuccessReturnPagination($notificationObject['data'], $notificationObject['totalPages'], $notificationObject['nextPageUrl'], 'NOTIFICATION_FETCHED');
    }


    public function readNotification(Request $request)
    {
        // validate rules for input
        $rules = [
            'notification_id' => ['required', 'numeric', 'positive_integer',  'iexists:notifications,id,received_by,' . Auth::user()->id],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);


        $notificationObject = Notification::where('received_by', Auth::id())->findOrFail($request->notification_id);

        $notificationObject->read = true;

        // if data not save show error
        PublicException::NotSave($notificationObject->save());


        return Helper::SuccessReturn($notificationObject, 'NOTIFICATION_READ');
    }
}
