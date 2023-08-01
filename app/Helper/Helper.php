<?php

namespace App\Helper;

use App\Exceptions\PublicException;
use App\Mail\SendMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function SuccessReturn($data = [], string $messageKey = '', array $replace = [], array $receiverIds = [])
    {
        if (DB::transactionLevel() > 0) {
            // Commit the transaction
            DB::commit();
        }
        $message = $messageKey ? __("message." . $messageKey, $replace) : '';
        $reponse = ['success' => true, 'status' => STATUS_OK, 'message' => $message, 'data' => $data];
        if ($receiverIds) {
            $reponse['receiver_ids'] = $receiverIds;
        }
        return response()->json($reponse, STATUS_OK);
    }


    public static function SuccessReturnPagination($data = [], $totalPage = 0, $nextPage = "", string $messageKey = '', array $replace = [])
    {

        $message = $messageKey ? __("message." . $messageKey, $replace) : '';
        return response()->json(['success' => true, 'status' => STATUS_OK, 'message' => $message, 'data' => $data, 'total_page' => $totalPage, 'next_page' => $nextPage], STATUS_OK);
    }

    public static function EmptyReturn(string $messageKey = '', array $replace = [])
    {
        $message = $messageKey ? __("message." . $messageKey, $replace) : '';
        return response()->json(['success' => false, 'status' => STATUS_OK, 'message' => $message, 'data' => null], STATUS_OK);
    }


    /**
     * Uploads a file to local or S3 storage.
     */
    public static function FileUpload(string $fileKey = '', array $fileInfo = [])
    {
        // Check if required parameters are provided.
        if (empty($fileKey) || empty($fileInfo) || empty($fileInfo['path']) || empty($fileInfo['storage'])) {
            return null;
        }

        // Check if file with specified key exists in request.
        if (!request()->hasFile($fileKey)) {
            return null;
        }

        // Generate a random filename and append timestamp and file extension to it.
        $fileName = $fileInfo['storage'] . '-' . randomString(20) . '-' . time() . '.' . request()->file($fileKey)->getClientOriginalExtension();

        // Upload the file to the specified storage.
        switch ($fileInfo['storage']) {
            case 'local':
                // Move the file to the specified path in local storage.
                return request()->file($fileKey)->move(public_path($fileInfo['path']), $fileName) ? $fileName : null;
            case 's3':
                // Check if S3 credentials are provided.
                if (empty(config('filesystems.disks.s3.key')) || empty(config('filesystems.disks.s3.secret'))) {
                    PublicException::Error('S3_BUCKET_CREDENTIALS');
                }
                // Store the file in S3 storage with the specified path and filename.
                return request()->file($fileKey)->storeAs($fileInfo['path'], $fileName, 's3');
            default:
                return null;
        }
    }

    /**
     * Uploads multiple file to local or S3 storage.
     */
    public static function MultiFileUpload(string $fileKey = '', array $fileInfo = [])
    {
        // Check if required parameters are provided.
        if (empty($fileKey) || empty($fileInfo) || empty($fileInfo['path']) || empty($fileInfo['storage'])) {
            return null;
        }

        // Check if file with specified key exists in request.
        if (!request()->hasFile($fileKey)) {
            return null;
        }


        // Initialize an empty array to hold image information.
        $imageArray = [];

        // Loop through each file in the array of files associated with the specified $fileKey.
        foreach (request()->file($fileKey) as $fileKeySingle) {

            // Generate a random filename and append a timestamp and file extension to it.
            $fileName = $fileInfo['storage'] . '-' . randomString(20) . '-' . time() . '.' . $fileKeySingle->getClientOriginalExtension();

            // Get the MIME type, extension, and file type (image, video, or audio).
            $mime = $fileKeySingle->getClientMimeType();
            $extension = $fileKeySingle->getClientOriginalExtension();
            $fileType = "";
            if (strstr($mime, "video/")) {
                $fileType = "video";
            } else if (strstr($mime, "image/")) {
                $fileType = "image";
            } else if (strstr($mime, "audio/")) {
                $fileType = "audio";
            }

            // Upload the file to the specified storage.
            switch ($fileInfo['storage']) {

                    // If local storage is specified, move the file to the specified path in the public directory.
                case 'local':
                    $fileName = $fileKeySingle->move(public_path($fileInfo['path']), $fileName) ? $fileName : null;
                    break;

                    // If S3 storage is specified, store the file in the specified bucket with the specified path and filename.
                case 's3':

                    // Check if S3 credentials are provided.
                    if (empty(config('filesystems.disks.s3.key')) || empty(config('filesystems.disks.s3.secret'))) {
                        PublicException::Error('S3_BUCKET_CREDENTIALS');
                    }

                    // Store the file in S3 storage.
                    $fileName = $fileKeySingle->storeAs($fileInfo['path'], $fileName, 's3');
                    break;

                    // If an unsupported storage type is specified, return null.
                default:
                    return null;
            }

            // Add the filename, file type, and file extension to the imageArray.
            $imageArray[] = [
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_extension' => $extension
            ];
        }
        return $imageArray;
    }


    /**
     * Generates the public link for a file.
     */
    public static function FilePublicLink(?string $fileName = '', array $fileInfo = [])
    {
        // Check if required parameters are provided.
        if (empty($fileInfo) || (empty($fileInfo['default']) && !$fileName)) {
            return null;
        }

        // If no filename is provided, return the default file link.
        if (!$fileName) {
            return asset('/default/' . $fileInfo['default']);
        }

        // Check if S3 bucket URL is provided if using S3 storage.
        if ($fileInfo['storage'] == 's3' && empty(config('filesystems.disks.s3.url'))) {
            PublicException::Error('S3_BUCKET_URL');
        }

        // Generate the public link for the file.
        $path = match ($fileInfo['storage']) {
            'local' => asset('/' . $fileInfo['path'] . '/' . $fileName),
            's3' => config('filesystems.disks.s3.url') . '/' . $fileName,
            default => null,
        };

        // Replace any repeated slashes in the path with a single slash.
        return $path ? preg_replace('#(^|[^:])//+#', '\\1/', $path) : null;
    }



    /**
     * Deletes a file from the specified storage.
     */
    public static function FileDelete(?string $file = '', array $fileInfo = [])
    {
        // Check if required parameters are provided.
        if (empty($file) || empty($fileInfo) || empty($fileInfo['path']) || empty($fileInfo['storage'])) {
            return null;
        }

        // Choose the storage to use based on the provided storage type.
        switch ($fileInfo['storage']) {
            case 'local':
                // Delete the file from the local storage.
                $filePath = public_path($fileInfo['path'] . '/' . $file);
                return file_exists($filePath) ? unlink($filePath) : false;

            case 's3':
                // Check if S3 credentials are provided.
                if (empty(config('filesystems.disks.s3.key')) || empty(config('filesystems.disks.s3.secret'))) {
                    PublicException::Error('S3_BUCKET_CREDENTIALS');
                }

                // Delete the file from the S3 storage.
                return Storage::disk('s3')->delete($file);
            default:
                return null;
        }
    }


    /**
     * Moves a file from a temp location to another location in the same storage.
     */
    public static function MoveS3BucketFile(?string $oldPath, array $fileInfo = []): ?string
    {
        // Check if required parameters are provided.
        if (empty($oldPath) || empty($fileInfo) || empty($fileInfo['path']) || empty($fileInfo['storage']) || strpos(trim($oldPath, '/'), 'temp/') !== 0) {
            return null;
        }

        switch ($fileInfo['storage']) {
            case 's3':
                // Check if S3 credentials are provided.
                if (!config('filesystems.disks.s3.key') || !config('filesystems.disks.s3.secret')) {
                    PublicException::Error('S3_BUCKET_CREDENTIALS');
                }

                // Generate a new file name.
                $newFileName = $fileInfo['storage'] . '-' . randomString(20) . '-' . time() . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = $fileInfo['path'] . '/' . $newFileName;
                if ($oldPath == $newPath) {
                    return null;
                }
                // Move the file within the S3 storage to the new file name.
                return Storage::disk('s3')->move($oldPath, $newPath) ? $newPath : null;

            default:
                return null;
        }
    }


    public static function UpdateObjectIfKeyExist(object $object,  array $keys)
    {
        foreach ($keys as $key) {
            if (request()->has($key)) {
                $object->$key = request()->$key;
            }
        }
        return $object;
    }

    public static function UpdateObjectIfKeyNotEmpty(object $object,  array $keys)
    {
        foreach ($keys as $key) {
            if (request()->has($key) && !empty(request()->$key)) {

                $data = request()->$key;
                 if(is_array($data)) {
                    // It's an array
                    $jsonData = json_encode($data);
                } elseif (is_string($data) && strpos($data, ',') !== false) {
                    // It's a string array
                    $dataArray = explode(',', $data);
                    $jsonData = json_encode($dataArray);
                } elseif (is_string($data)) {
                    // It's a normal string
                    $jsonData = $data;
                }else{
                    $jsonData = $data;
                }
                $object->$key = $jsonData;


            }
        }
        return $object;
    }


    /**
     * Sends email using Laravel's built-in Mail facade
     */
    public static function SendMail(array $data = [], string $mailClass = 'SendMail')
    {
        // Check if email credentials are set in the configuration
        if (empty(config('mail.mailers.smtp.username')) || empty(config('mail.mailers.smtp.password'))) {
            PublicException::Error('MAIL_CREDENTIALS');
        }

        if ($data) {
            switch ($mailClass) {
                case 'SendMail': {
                        // Send email using the SendMail class
                        return Mail::to($data['to'])->send(new SendMail($data));
                    }
                default:
                    return null;
            }
        }
        return null;
    }

    /**
     * Sends SMS messages using Twilio's REST API
     */
    public static function SendMessage(array $phoneNumbers = [], string $messageBody = '')
    {
        // Check if Twilio credentials are set in the configuration
        if (empty(config('settings.twilio.twilio_sid')) || empty(config('settings.twilio.twilio_auth_token'))  || empty(config('settings.twilio.twilio_number'))) {
            PublicException::Error('SMS_CREDENTIALS');
        }

        // Get Twilio credentials from the configuration
        $accountSid = config('settings.twilio.twilio_sid');
        $authToken = config('settings.twilio.twilio_auth_token');
        $twilioNumber = config('settings.twilio.twilio_number');

        // Create a new Twilio REST client
        $client = new Client($accountSid, $authToken);

        $messages = [];
        foreach ($phoneNumbers as $to) {
            // Send SMS message to each phone number
            $messages[] = $client->messages->create(
                $to,
                [
                    'from' => $twilioNumber,
                    'body' => $messageBody
                ]
            );
        }

        return array_column($messages, 'sid');
    }


    /**
     * Sets a geolocation column in an object using longitude and latitude values
     */
    public static function MakeGeolocation(object $object, $longitude, $latitude, $column = 'geolocation')
    {
        // Check if both longitude and latitude values are set
        if (!empty($longitude) && !empty($latitude)) {
            // Set the geolocation column using a raw SQL statement
            $object->$column = DB::raw("ST_MakePoint($longitude, $latitude)");
        }
        return $object;
    }


    /**
     * Search for objects within a specified radius of a given geolocation point
     */
    public static function SearchGeolocation(object $object, $longitude, $latitude, $radius, $column = 'geolocation')
    {
        // Check if both longitude, latitude, and radius values are set
        if (!empty($longitude) && !empty($latitude) && !empty($radius)) {
            // Convert radius from miles to meters
            $distance = $radius * 1000 * 1.60934;
            // Use PostgreSQL's ST_DWithin function to search for objects within the given radius
            $object->whereRaw("ST_DWithin(" . $column . ", ST_SetSRID(ST_Point(" . $longitude . ", " . $latitude . "), 4326), " . $distance . ")");
        }
        return $object;
    }


    public static function Datatable(array $datatable, object $modelObject, $defaultOrder = ['created_at', 'desc'])
    {
        // Validate datatable array
        foreach (['column', 'search_column', 'order_column'] as $key) {
            if (!isset($datatable[$key]) || !is_array($datatable[$key])) {
                throw new PublicException($key . ' not added or not array in datatable array', STATUS_GENERAL_ERROR);
            }
        }

        // Check search and order columns exist in column array
        foreach (['search_column', 'order_column'] as $type) {
            foreach ($datatable[$type] as $key) {
                if (!array_key_exists($key, $datatable['column'])) {
                    throw new PublicException("$type key $key not added in datatable column key array", STATUS_GENERAL_ERROR);
                }
            }
        }

        $datatable['default_order_column'] = [$defaultOrder[0] => $defaultOrder[1]];

        // If request()->column is true, return datatable as JSON response
        if (request()->column == "true") {
            throw new HttpResponseException(response()->json($datatable), STATUS_OK);
        }

        // Get datatable parameters from request
        $start = (int) request()->start;
        $length = (int) request()->length <= 100 ? (int) request()->length : 10;
        $searchValue = (string) request()->input('search.value');
        $orderColumn = (int) request()->input('order.0.column');
        $orderColumnName = $datatable['order_column'][$orderColumn] ?? $defaultOrder[0];
        $orderDirection = in_array(request()->input('order.0.dir'), ['asc', 'desc']) ? request()->input('order.0.dir') : $defaultOrder[1];

        $totalRecords = $modelObject->count();

        $searchColumnArray = [];
        foreach ($datatable['search_column'] as $str) {
            $keys = explode('.', $str);
            $temp = &$searchColumnArray;

            foreach ($keys as $index => $key) {
                if ($index == count($keys) - 1) {
                    $temp[] = $key;
                } else {
                    if (!isset($temp[$key])) {
                        $temp[$key] = [];
                    }
                    $temp = &$temp[$key];
                }
            }
        }

        if ($searchValue) {
            $modelObject->where(function ($query) use ($searchColumnArray, $searchValue) {
                self::DatatableSearch($query, $searchColumnArray, $searchValue);
            });
            $filteredRecords = $modelObject->count();
        } else {
            $filteredRecords = $totalRecords;
        }

        $modelObject->skip($start)->take($length)->orderBy($orderColumnName, $orderDirection);

        return compact('modelObject', 'filteredRecords', 'totalRecords', 'start', 'length');
    }


    public static function DatatableSearch(object $modelObject, array $searchColumnArray, string $searchValue)
    {
        $first = true;
        foreach ($searchColumnArray as $key => $columnName) {
            if (is_numeric($key)) {
                if ($first) {
                    $modelObject->whereRaw('CAST(' . $columnName . ' AS VARCHAR) LIKE ?', ['%' . $searchValue . '%']);
                    $first = false;
                } else {
                    $modelObject->orWhereRaw('CAST(' . $columnName . ' AS VARCHAR) LIKE ?', ['%' . $searchValue . '%']);
                }
            } else {
                $modelObject->with($key);
                $modelObject->orWhereHas($key, function ($query) use ($columnName, $searchValue) {
                    self::DatatableSearch($query, $columnName, $searchValue);
                });
            }
        }
    }
}
