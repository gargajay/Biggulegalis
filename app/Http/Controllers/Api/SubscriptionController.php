<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function setSubscriptionPlan(Request $request)
    {
        // validate rules for input
        $rules = [
            'subscription_plan_id' => ['required', 'numeric', 'positive_integer', 'digits_between:1,18', 'iexists:subscription_plans,id,category,' . SUBSCRIPTION_PLAN_CATEGORY['Premium Plan']],
        ];

        // validate input data using the Validator method of the PublicException class
        PublicException::Validator($request->all(), $rules);

        // Begin database transaction
        DB::beginTransaction();

        if ($request->subscription_plan_id != Auth::user()->default_subscription_plan_id) {

            $subscriptionPlanObject = SubscriptionPlan::find($request->subscription_plan_id);
            $extraEmployee =  Auth::user()->total_employee - $subscriptionPlanObject->max_users;
            if ($subscriptionPlanObject->max_users && $extraEmployee > 0) {
                return PublicException::CustomError('SUBSCRIPTION_REMOVE_EXTRA_EMPLOYEE', ['employee_count' => $extraEmployee], data: ['extra_employee' => true]);
            }

            $userObject = User::find(Auth::id());

            $userObject->default_subscription_plan_id = $request->subscription_plan_id;
            $userObject->cancel_subscription = false;

            // if data not save show error
            PublicException::NotSave($userObject->save());

            Subscription::subscribePlan(Auth::id());
        }
        return Helper::SuccessReturn(Auth::user()->subscription, 'SUBSCRIPTION_PLAN_SET');
    }


    public function cancelSubscriptionPlan(Request $request)
    {
        $userObject = User::find(Auth::id());
        $userObject->cancel_subscription = true;
        $userObject->default_subscription_plan_id = null;
        // if data not save show error
        PublicException::NotSave($userObject->save());

        return Helper::SuccessReturn(Auth::user()->subscription, 'SUBSCRIPTION_CANCEL');
    }
}
