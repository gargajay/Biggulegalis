<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAssociation
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
        if (isset(auth()->user()->userAssociation) && auth()->user()->userAssociation->status==2) {
            return response()->json(['success' => FALSE, 'status' =>400, 'message' => __("message.Inviation_not_accept")], 200);

        }
        return $next($request);
    }
}
