<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if (auth()->user()->subscription['subscription_status'] == false) {
        //     if (auth()->user()->subscription['is_company'] == false) {
        //         return response()->json(['success' => FALSE, 'status' => PAYMENT_REQUIRED, 'message' => __("message.SUBSCRIPTION_EXPIRED_EMPLOYEE")], PAYMENT_REQUIRED);
        //     }
        //     return response()->json(['success' => FALSE, 'status' => PAYMENT_REQUIRED, 'message' => __("message.SUBSCRIPTION_EXPIRED")], PAYMENT_REQUIRED);
        // }
        return $next($request);
    }
}
