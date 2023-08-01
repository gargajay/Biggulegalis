<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogAfterRequest
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
        return $next($request);
    }


    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $middlewares = $request->route()->middleware();
        if (!in_array('SkipLogAfterRequest', $middlewares)) {
            Log::info('app.requests', ['path' => $request->getPathInfo(), 'ip' => $request->ip(), 'request' => $request->all(), 'user_id' => Auth::check() ? Auth::id() : '']);
        } else {
            Log::info('app.requests', ['path' => $request->getPathInfo(), 'ip' => $request->ip(), 'user_id' => Auth::check() ? Auth::id() : '']);
        }
    }
}
