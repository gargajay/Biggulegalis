<?php

namespace App\Exceptions;

use App\Helper\Stripe;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
    {
        $largeException = parent::render($request, $exception);
        $statusCode = $largeException->getStatusCode();

        // Roll back the transaction
        DB::rollBack(DB::transactionLevel());

        if ($stripeException = Stripe::stripeException($exception)) {
            return response()->json($stripeException, STATUS_OK);
        }

        if ($exception instanceof PublicException) {
            return $exception->render($exception->getMessage(), $statusCode , $exception->getData());
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['success' => FALSE, 'status' => STATUS_OK, 'message' => __("message.NOT_FOUND")], STATUS_OK);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['success' => FALSE, 'status' => TOO_MANY_REQUESTS, 'message' => __("message.TOO_MANY_ATTEMPTS")], TOO_MANY_REQUESTS);
        }

        if ($request->route() && $request->route()->getPrefix() == "api") {

            if ($exception instanceof AuthenticationException) {
                return response()->json(['success' => FALSE, 'status' => STATUS_UNAUTHORIZED, 'message' => __("message.UNAUTHORIZED_ACCESS")], STATUS_UNAUTHORIZED);
            }

            $response = [];
            $response['message'] = 'An error has occurred. Please contact support for assistance. Error code: ' . $statusCode;
            $response['success'] = FALSE;
            $response['status'] = $statusCode;
            if (config('app.debug')) {
                $response['trace'] = $exception->getMessage();
                $response['line'] = $exception->getLine();
                $response['file'] = $exception->getFile();
                $response['code'] = $exception->getCode();
            }

            Log::error($exception->getMessage(), [
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'reqest_data' => request()->all(),
                'auth_user' => !Auth::check() ? [] : [
                    'id' => Auth::user()->id,
                    'full_name' => Auth::user()->full_name,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'email' => Auth::user()->email,
                    'country_code' => Auth::user()->country_code,
                    'phone' => Auth::user()->phone,
                    'user_type' => Auth::user()->user_type,
                ],
            ]);

            return response()->json($response, $statusCode);
        }

        if ($exception instanceof AuthenticationException) {
            return redirect('/');
        }

        Log::error($exception->getMessage(), [
            'url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reqest_data' => request()->all(),
            'auth_user' => !Auth::check() ? [] : [
                'id' => Auth::user()->id,
                'full_name' => Auth::user()->full_name,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'email' => Auth::user()->email,
                'country_code' => Auth::user()->country_code,
                'phone' => Auth::user()->phone,
                'user_type' => Auth::user()->user_type,
            ],
        ]);

        return parent::render($request, $exception);
    }

    public function report(Throwable $exception)
    {
        // Skip logging validation exceptions
        if ($exception instanceof PublicException) {
            Log::info('PublicException', [
                'message' => $exception->getMessage(),
                'url' => request()->fullUrl(),
                'reqest_data' => request()->all(),
                'auth_user' => !Auth::check() ? [] : [
                    'id' => Auth::user()->id,
                    'full_name' => Auth::user()->full_name,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'email' => Auth::user()->email,
                    'country_code' => Auth::user()->country_code,
                    'phone' => Auth::user()->phone,
                    'user_type' => Auth::user()->user_type,
                ],
            ]);
            return;
        }
        if ($exception instanceof AuthenticationException) {
            return;
        }
        parent::report($exception);
    }
}
