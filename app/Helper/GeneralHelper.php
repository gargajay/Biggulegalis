<?php

use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('IsEmpty')) {
    function IsEmpty($object)
    {
        return empty($object) || (is_object($object) && $object->count() === 0);
    }
}

if (!function_exists('generateOTP')) {
    function generateOTP($otpLength)
    {
        $min = pow(10, $otpLength - 1);
        $max = pow(10, $otpLength) - 1;
        return mt_rand($min, $max);
    }
}

if (!function_exists('secondsToTimeFormat')) {
    function secondsToTimeFormat($seconds, $format = [' hours ', ' minutes ', ' seconds '])
    {
        // Convert to hours, minutes, and seconds
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        // Format the result
        $timeFormat = ($hours ? number_format($hours) . $format[0] : '') . ($minutes ? number_format($minutes) . $format[1] : '') . ($seconds ? number_format($seconds) . $format[2] : '');
        $timeFormat = $timeFormat ? $timeFormat : '0 ' . $format[2];
        // Output the result
        return trim(str_replace('  ', ' ', $timeFormat));
    }
}



if (!function_exists('randomString')) {
    function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }
}


if (!function_exists('dynamicCacheVersion')) {
    function dynamicCacheVersion($assetPath = '')
    {
        $folderPath = dirname(dirname(dirname(__FILE__)));
        $filePath = $folderPath . '/public/' . $assetPath;
        $assetUrl = $assetPath;

        if (file_exists($filePath)) {
            $lastModificationTime = filemtime($filePath);
            $assetUrl .= '?lmt=' . date("YmdHis", $lastModificationTime);
        }
        return asset(str_replace('//', '/', $assetUrl));
    }
}

if (!function_exists('haveValue')) {

    /**
     * Check if any of the keys in an array have a non-null, non-empty value.
     */
    function haveValue(array $data, array $keys): bool
    {
        // Loop through each key in the array of keys.
        foreach ($keys as $key) {
            // If the key exists in the data array and has a non-null, non-empty value, return true.
            if (isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null) {
                return true;
            }
        }
        // If none of the keys have a value, return false.
        return false;
    }
}


if (!function_exists('lastOneYearMontlyData')) {
    /**
     * Get monthly data for the last 12 months.
     */
    function lastOneYearMontlyData(object $mobel)
    {
        $monthName = Carbon\Carbon::now()->subMonths(1)->format('M-y');

        $data = $mobel->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, \'MM\') as month , MAX(id) as id')
            ->whereRaw("DATE(created_at) BETWEEN ? AND ?", [
                Carbon\Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d'),
                Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')
            ])->groupByRaw('DATE_FORMAT(created_at, \'MM\')')->orderBy('id', 'asc')->get()->toArray();

        // Create an associative array with month as key and data count as value
        $data = array_combine(array_column($data, 'month'), array_column($data, 'count'));
        // Generate monthly data for the last 12 months
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            // Get the month number and name
            $monthNum = Carbon\Carbon::now()->startOfMonth()->subMonths($i)->format('m');
            $monthName = Carbon\Carbon::now()->startOfMonth()->subMonths($i)->format('M-y');

            // Add monthly data to the array
            $monthlyData[] = ['month' => $monthName, 'total' => isset($data[$monthNum]) ? $data[$monthNum] : 0];
        }
        return $monthlyData;
    }
}


if (!function_exists('arrayToUL')) {
    function arrayToUL($array)
    {
        $html = "<ul style=\"margin : 0\">";
        foreach ($array as $item) {
            if (!empty($item)) {
                if (is_array($item)) {
                    $html .= "<li>" . arrayToUL($item) . "</li>";
                } else {
                    $html .= "<li>" . $item . "</li>";
                }
            }
        }
        $html .= "</ul>";
        return $html;
    }
}


if (!function_exists('isJson')) {
    function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        // Attempt to decode the JSON string
        $json = json_decode($string);

        // Check for errors during decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        // Check that the decoded value is actually an object or array
        if (!is_array($json) && !is_object($json)) {
            return false;
        }

        return true;
    }
}


if (!function_exists('optionButton')) {
    function optionButton(string $type = null, string $route = '', $data = null)
    {
        if ($type && $route) {
            switch ($type) {
                case 'edit':
                    return '<button class="btn btn-github btn-sm datatable-option-btn modal-link" type="button"  data-url="' . $route . '"><i class="fa fa-pencil"></i></button>';
                case 'status':
                    return '<button class="btn btn-facebook btn-sm datatable-option-btn status-link" type="button"   data-url="' . $route . '"><i class="fa fa-toggle-' . ($data['status'] == 'Active' ? 'on' : 'off') . '"></i></button>';
                case 'delete':
                    return '<button class="btn btn-danger btn-sm deleteBtn datatable-option-btn delete-link" type="button"   data-url="' . $route . '"><i class="fa fa-trash"></i></button>';
                case 'detail':
                    return '<button class="btn btn-twitter btn-sm datatable-option-btn modal-link" type="button"  data-url="' . $route . '"><i class="fa fa-file-text-o"></i></button>';
            }
        }
    }
}

if (!function_exists('colorDarken')) {
    function colorDarken(string $colorCode = null, int $darken = null)
    {
        if ($colorCode && $darken) {
            // Convert the color code to RGB values
            list($r, $g, $b) = sscanf($colorCode, "#%02x%02x%02x");

            // Make the color darker or lighter by adjusting the RGB values
            // Amount to darken the color by (can be negative for lightening)
            $r = max(0, $r - $darken);
            $g = max(0, $g - $darken);
            $b = max(0, $b - $darken);

            // Convert the modified RGB values back to a color code
            return $newColorCode = sprintf("#%02x%02x%02x", $r, $g, $b);
        }
        return '';
    }
}


if (!function_exists('getDarkenValue')) {
    function getDarkenValue(string $initialColorCode, string $targetColorCode): int
    {
        // Convert the color codes to RGB values
        list($r1, $g1, $b1) = sscanf($initialColorCode, "#%02x%02x%02x");
        list($r2, $g2, $b2) = sscanf($targetColorCode, "#%02x%02x%02x");

        // Calculate the differences between the RGB values
        $dr = $r1 - $r2;
        $dg = $g1 - $g2;
        $db = $b1 - $b2;

        // Get the maximum difference and use it as the darken value
        $darken = max($dr, $dg, $db);

        return max(0, $darken);
    }
}


if (!function_exists('customTrans')) {
    /**
     * Returns a string with translated messages based on a given array of data
     *
     * eq  $messageArray = ['helo','LOGIN_FAILED','FORGOT_EMAIL_RESEND'=>['seconds'=>5]];
     *     $locale = 'es';
     */
    function customTrans(?array $messageArray = null, ?string $locale = null, string $langFile = 'message'): string
    {
        if (!$messageArray) {
            return '';
        }

        $translatedMessages = [];
        if ($messageArray) {
            foreach ($messageArray as  $key => $value) {
                if (is_numeric($key)) {
                    $key = $langFile . "." . $value;
                    $translatedMessages[] = trans($key, [], $locale) === $key ? $value : trans($key, [], $locale);
                } else {
                    $key = $langFile . "." . $key;
                    $translatedMessages[] = trans($key, $value, $locale);
                }
            }
        }
        return implode(' ', $translatedMessages);
    }
}


if (!function_exists('selected')) {

    function selected($firstValue = null, $secondValue = null): string
    {
        return (!is_null($firstValue) && $firstValue == $secondValue) ? 'selected' : '';
    }
}


if (!function_exists('paginate')) {

    /**
     * Paginates the given model object and returns the paginated results
     */
    function paginate(object $modelObject, int $page = 1, int $limit = 10, array $append = []): array
    {
        // Get the page number from the request or set it to 1 by default
        $page = (int) request()->page ? (int) request()->page : $page;

        // Get the limit from the request or set it to 10 by default
        $limit = (int) request()->limit ? (int) request()->limit : $limit;

        // Paginate the model object using the limit and page number
        $paginatedResults = $modelObject->paginate($limit, ['*'], 'page', $page);

        if ($append) {
            $paginatedResults->append($append);
        }

        // Return the paginated results along with metadata
        return [
            'data' => $paginatedResults->items(),
            'total' => $paginatedResults->total(),
            'per_page' => $paginatedResults->perPage(),
            'current_page' => $paginatedResults->currentPage(),
            'last_page' => $paginatedResults->lastPage(),
        ];
    }
}


/**
 * Updates a value in the request data and merges it back into the request object.
 */
if (!function_exists('updateRequestValue')) {
    function updateRequestValue($key = null, $value = null)
    {
        // Check if the key and value parameters are not null
        if (!is_null($key) && !is_null($value) && $value != '') {
            // Get all the data from the current request
            $data = request()->all();

            // Check if the key exists in the data
            if (array_key_exists($key, $data)) {
                // Set the new value for the key in the request object and the data array
                request()->$key = $data[$key] = $value;

                // Merge the updated data array back into the request object
                request()->merge($data);

                // Return true to indicate success
                return true;
            }
        }

        // Return false to indicate failure
        return false;
    }
}


/**
 * Get the formatted date with a specified timezone.
 */
if (!function_exists('formatDateWithTimezone')) {
    function formatDateWithTimezone(?string $datetime, string $format = 'Y-m-d H:i:s', ?string $timezone = null): ?string
    {
        if (!is_null($datetime)) {
            // If no timezone is specified, use the client's timezone, the user's timezone, or UTC in that order.
            $timezone = $timezone ?? $_SERVER['HTTP_X_TIMEZONE'] ?? auth()->user()->timezone ?? 'UTC';

            // Parse the input date and time and format it with the specified timezone.
            $datetime = Carbon\Carbon::parse($datetime)->setTimezone($timezone)->format($format);
        }

        return $datetime;
    }
}



/**
 * Retrieve the translation for a given key and replace placeholders with provided values.
 */
function words(?string $key = null, array $replace = [], ?string $locale = null): ?string
{
    // If no key is provided, return null
    if (!$key) {
        return null;
    }

    // Construct the complete translation keys
    $completeKeys = ["words." . $key, "words." . strtoupper($key), "words." . strtolower($key)];

    // Loop through each key and return the translation if found
    foreach ($completeKeys as $completeKey) {
        $translation = trans($completeKey, $replace, $locale);
        if ($translation !== $completeKey) {
            return $translation;
        }
    }

    // If no translation was found, return the original key
    return $key;
}




if (!function_exists('isJson')) {
    function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        // Attempt to decode the JSON string
        $json = json_decode($string);

        // Check for errors during decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        // Check that the decoded value is actually an object or array
        if (!is_array($json) && !is_object($json)) {
            return false;
        }

        return true;
    }
}
if (!function_exists('customTrans')) {
    /**
     * Returns a string with translated messages based on a given array of data
     *
     * eq  $messageArray = ['helo','LOGIN_FAILED','FORGOT_EMAIL_RESEND'=>['seconds'=>5]];
     *     $locale = 'es';
     */
    function customTrans(?array $messageArray = null, ?string $locale = null, string $langFile = 'message'): string
    {
        if (!$messageArray) {
            return '';
        }

        $translatedMessages = [];
        if ($messageArray) {
            foreach ($messageArray as  $key => $value) {
                if (is_numeric($key)) {
                    $key = $langFile . "." . $value;
                    $translatedMessages[] = trans($key, [], $locale) === $key ? $value : trans($key, [], $locale);
                } else {
                    $key = $langFile . "." . $key;
                    $translatedMessages[] = trans($key, $value, $locale);
                }
            }
        }
        return implode(' ', $translatedMessages);
    }
}

if (!function_exists('newPagination')) {

    /**
     * Paginates the given model object and returns the paginated results
     */
    function newPagination(object $modelObject, $page = 10): array
    {
        // Paginate the model object using the limit and page number
        $count = $modelObject->count();
        $paginatedResults = $modelObject->paginate($page);

        // Return the paginated results along with metadata
        return [
            'data' => $paginatedResults->items(),
            'totalPages' => $count > 0 ? $paginatedResults->lastPage() : 0,
            'nextPageUrl' => $paginatedResults->nextPageUrl(),
        ];
    }
}

if (!function_exists('customPaginate')) {

    /**
     * Paginates the given model object and returns the paginated results
     */
    function customPaginate(array $modelObject, int $page = 1)
    {
        $perPage = 2; // Number of items per page
        $offset = ($page * $perPage) - $perPage;
        $count = count($modelObject);
        $paginatedResults =  new LengthAwarePaginator(
            array_slice($modelObject, $offset, $perPage),
            count($modelObject), // Total items
            $perPage, // Items per page
            $page, // Current page
        );

        // Return the paginated results along with metadata
        return [
            'data' => $paginatedResults->items(),
            'totalPages' => $count > 0 ? $paginatedResults->lastPage() : 0,
            'nextPageUrl' => $paginatedResults->nextPageUrl(),
        ];
    }
}