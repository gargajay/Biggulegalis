<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class PublicException extends Exception
{
    protected $data;

    public function __construct($message = '', $statusCode = STATUS_OK, $data = [])
    {
        parent::__construct($message, $statusCode);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }


    public function render($message = '', $statusCode = STATUS_OK, $data = [])
    {
        $responseJson = [
            'success' => FALSE,
            'status' => 400,
            'message' => $message,
        ];
        if ($data) {
            $responseJson['data'] = $data;
        }
        return response()->json($responseJson, $statusCode);
    }


    public static function Validator(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            throw new PublicException($validator->errors()->first(), STATUS_OK);
        }
    }

    public static function Error(string $localizationKey = '', $statusCode = STATUS_OK, $data = [])
    {
        if ($localizationKey) {
            throw new PublicException(__("message." . $localizationKey), $statusCode, $data);
        }
    }

    public static function NotSave($stateStatus, string $localizationKey = 'SOMETHING_WENT_WRONG', $statusCode = STATUS_OK)
    {
        if (!$stateStatus) {
            throw new PublicException(__("message." . $localizationKey), $statusCode);
        }
    }

    public static function CustomError(string $localizationKey = '', array $replace = [], $statusCode = STATUS_OK, $data = [])
    {
        if ($localizationKey) {
            throw new PublicException(trans("message." . $localizationKey, $replace), $statusCode, $data);
        }
    }

    public static function Empty($object, string $localizationKey = '', $statusCode = STATUS_OK)
    {
        if (IsEmpty($object)) {
            $localizationKey = $localizationKey ? $localizationKey : 'NOT_FOUND';
            throw new PublicException(__("message." . $localizationKey), $statusCode);
        }
    }

    public static function ErrorWebPage(string $localizationKey = '', $statusCode = '')
    {
        if ($localizationKey && $statusCode) {
            $response = new Response(view('web.error.error', ['title' => __("message.RESET_LINK_EXPIRED.title"), 'message' => __("message.RESET_LINK_EXPIRED.message"), 'status_code' => STATUS_LINK_EXPIRED]));
            throw new HttpResponseException($response);
        }
    }
}
