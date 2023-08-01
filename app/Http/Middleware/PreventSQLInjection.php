<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreventSQLInjection
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            $input = $this->xss_clean($input);
        });
        $request->merge($input);
        return $next($request);
    }


    public function xss_clean($data)
    {
        if (base64_decode($data, true)) {
            return $data;
        }
        $data = app('db')->getPdo()->quote($data);
        return substr($data, 1, -1);

    }
}