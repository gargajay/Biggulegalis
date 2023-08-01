<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlanController extends Controller
{
    public function getSubscriptionPlan(Request $request)
    {
        $subscriptionPlanObject = SubscriptionPlan::orderBy('sort_order', 'asc')->get()->append(['display_amount', 'active_plan']);

        return Helper::SuccessReturn(['data' => $subscriptionPlanObject, 'subscription' => Auth::user()->subscription], 'GET_SUBSCRIPTION_PLAN');
    }
}
