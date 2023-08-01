<?php


defined('GENDER') or define("GENDER", ['male', 'female','other']);
defined('ACCOUNT_TYPE') or define("ACCOUNT_TYPE", ['NORMAL' => 1, 'SOCIAL' => 2, 'BOTH' => 3]);
defined('USER_TYPE') or define("USER_TYPE", ['USER' => 'user', 'ADMIN' => 'admin']);
defined('DEVICE_TYPE') or define("DEVICE_TYPE", ['ANDROID' => 'A', 'IOS' => 'I']);
defined('SOCIAL_PLATFORM') or define("SOCIAL_PLATFORM", [1 => 'facebook_id', 2 => 'google_id', 3 => 'apple_id', 4 => 'twitter_id', 5 => 'instagram_id']);
defined('OTP_MODE') or define("OTP_MODE", ['EMAIL' => '1', 'SMS' => '2']);
defined('OTP_PURPOSE') or define("OTP_PURPOSE", ['FORGOT' => 1]);
defined('PUSH_NOTIFICATION_USER_SETTING') or define("PUSH_NOTIFICATION_USER_SETTING", ['ON' => '1', 'OFF' => '2']);
defined('PROFILE_COMPLETE') or define("PROFILE_COMPLETE", ['NO' => '1', 'YES' => '2']);
defined('TRANSLATE_LANGUAGE') or define("TRANSLATE_LANGUAGE", ['en' => 'English', 'es' => 'Spanish']);
defined('TRANSLATE_LANGUAGE_EXCEPT_ENGLISH') or define("TRANSLATE_LANGUAGE_EXCEPT_ENGLISH", ['es' => 'Spanish']);
defined('USER_SETTING_KEYS') or define("USER_SETTING_KEYS", ['MAP_SETTINGS']);
defined('ADDRESS_TYPE') or define('ADDRESS_TYPE', ['USER_ADDRESS' => 'User', 'EVENT_ADDRESS' => 'Event']);
defined('MIMES_TYPE') or define('MIMES_TYPE', ['IMAGE' => 'mimes:jpg,png,jpeg,gif', 'AUDIO' => 'mimes:mp3,mp4,m4a', 'VIDEO' => 'mimes:mp4,mov,avi']);

defined('SUBSCRIPTION_PLAN_INTERVAL') or define('SUBSCRIPTION_PLAN_INTERVAL', ['Day' => '1', 'Week' => '2', 'Month' => '3', 'Year' => '4']);
defined('SUBSCRIPTION_PLAN_CATEGORY') or define('SUBSCRIPTION_PLAN_CATEGORY', ['Free Plan' => '1', 'Premium Plan' => '2']);
defined('SUBSCRIPTION_CURRENCIES') or define('SUBSCRIPTION_CURRENCIES', ['USD' => 'usd']);
defined('SUBSCRIPTION_CURRENCY_SYMBOL') or define('SUBSCRIPTION_CURRENCY_SYMBOL', ['usd' => '$']);

defined('SUBSCRIPTION_STATUS') or define('SUBSCRIPTION_STATUS', ['Not Active' => '0', 'Active' => '1', 'Canceled' => '2', 'Expired' => '3']);
defined('SUBSCRIPTION_PAYMENT_STATUS') or define('SUBSCRIPTION_PAYMENT_STATUS', ['Pending' => '1', 'Succeeded' => '2', 'Failed' => '3']);
