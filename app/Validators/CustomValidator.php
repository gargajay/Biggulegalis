<?php

namespace App\Validators;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomValidator
{
    // custom validation rules and methods go here

    public static function ValidateRule()
    {
        // convert value in lower case and then check in database
        Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0]);
            $query->where($parameters[1], "LIKE", $value);

            $i = 2;
            while (isset($parameters[$i + 1])) {
                if (count(explode('~', $parameters[$i + 1])) > 1) {
                    $query->whereIn($parameters[$i], explode('~', $parameters[$i + 1]));
                } else {
                    $query->where($parameters[$i], "LIKE", $parameters[$i + 1]);
                }
                $i += 2;
            }

            // Add a condition to exclude the current user ID
            if (isset($parameters[$i])) {
                $query->where('id', '<>', intval($parameters[$i]));
            }

            $query->whereNull('deleted_at');
            return !$query->count();
        });


        // convert value in lower case and then check in database
        Validator::extend('iexists', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0]);
            $query->where($parameters[1], "LIKE", $value);
            $i = 2;
            while (isset($parameters[$i + 1])) {
                if (count(explode('~', $parameters[$i + 1])) > 1) {
                    $query->whereIn($parameters[$i], explode('~', $parameters[$i + 1]));
                } else {
                    $query->where($parameters[$i], "LIKE", $parameters[$i + 1]);
                }
                $i += 2;
            }

            // Add a condition to exclude the current user ID
            if (isset($parameters[$i])) {
                $query->where('id', '<>', intval($parameters[$i]));
            }

            $query->whereNull('deleted_at');
            return $query->count() ? true : false;
        });

        // Create a new validation rule to check for a number with commas
        Validator::extend('comma_separated_number', function ($attribute, $value, $parameters, $validator) {
            return is_numeric(str_replace(',', '', $value));
        });

        // Create a new validation rule to check for positive integer number
        Validator::extend('positive_integer', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]+$/', $value);
        });

        // Create a new validation rule to check positive decimal number
        Validator::extend('positive_decimal', function ($attribute, $value, $parameters, $validator) {
            return (is_numeric($value) && $value >= 0);
        });

        // Create a new validation rule to check latitude
        Validator::extend('latitude', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value >= -90 && $value <= 90;
        });

        // Create a new validation rule to check longitude
        Validator::extend('longitude', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value >= -180 && $value <= 180;
        });

        // Create a new validation rule to check phone number
        Validator::extend('phone_verify', function ($attribute, $value, $parameters, $validator) {
            // Get the country code from the validator's data, or set it to null if it doesn't exist
            $countryCode = $validator->getData()[$parameters[0]] ?? null;

            // If no country code is provided, validate the phone number as is
            if (!$countryCode) {
                return phone($value)->isValid();
            }

            // Prepend the country code to the phone number and validate it
            return phone($countryCode . $value)->isValid();
        });


        Validator::extend('color_code', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3}|[A-Fa-f0-9]{8})$/', $value);
        });


        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            $totalError = 0;
            $errorCode = '';

            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                $errorCode = 'special_char_required';
                $totalError++;
            }

            if (!preg_match('/[A-Z]/', $value)) {
                $errorCode = 'uppercase_required';
                $totalError++;
            }

            if (!preg_match('/[a-z]/', $value)) {
                $errorCode = 'lowercase_required';
                $totalError++;
            }

            if (!preg_match('/[0-9]/', $value)) {
                $errorCode = 'number_required';
                $totalError++;
            }

            if (strlen($value) < 8) {
                $errorCode = 'min_length';
                $totalError++;
            }

            if (strlen($value) > 50) {
                $errorCode = 'max_length';
                $totalError++;
            }

            if ($totalError > 1) {
                $errorCode = 'strong_password';
            }

            if ($errorCode) {
                $validator->addFailure($attribute, $errorCode, compact('attribute'));
            }
            return $totalError < 1;
        });


        Validator::extend('valid_json', function ($attribute, $value, $parameters, $validator) {
            return isJson($value);
        });

        Validator::extend('s3_file_exists', function ($attribute, $value, $parameters, $validator) {
            // Check if the file path exists in the S3 bucket
            return Storage::disk('s3')->exists($value);
        });
    }
}
