<?php

//App Info
defined('APP_NAME') or define("APP_NAME", 'Strengthen');


// response status 
defined('STATUS_BAD_REQUEST') or define("STATUS_BAD_REQUEST", 400);
defined('STATUS_UNAUTHORIZED') or define("STATUS_UNAUTHORIZED", 401);
defined('STATUS_CREATED') or define("STATUS_CREATED", 201);
defined('STATUS_OK') or define("STATUS_OK", 200);
defined('STATUS_GENERAL_ERROR') or define("STATUS_GENERAL_ERROR", 500);
defined('STATUS_FORBIDDEN') or define("STATUS_FORBIDDEN", 403);
defined('STATUS_NOT_FOUND') or define("STATUS_NOT_FOUND", 404);
defined('STATUS_METHOD_NOT_ALLOWED') or define("STATUS_METHOD_NOT_ALLOWED", 405);
defined('STATUS_ALREADY_EXIST') or define("STATUS_ALREADY_EXIST", 409);
defined('UNPROCESSABLE_ENTITY') or define("UNPROCESSABLE_ENTITY", 422);
defined('STATUS_LINK_EXPIRED') or define("STATUS_LINK_EXPIRED", 410);
defined('TOO_MANY_REQUESTS') or define("TOO_MANY_REQUESTS", 429);
defined('PAYMENT_REQUIRED') or define("PAYMENT_REQUIRED", 402);


// upload file information
// storage: "local", "s3"
defined('USER_IMAGE_INFO') or define("USER_IMAGE_INFO", ['path' => 'upload/user/image', 'storage' => 'local', 'default' => 'user.jpg']);
defined('APP_IMAGE_INFO') or define("APP_IMAGE_INFO", ['path' => 'upload/app/image', 'storage' => 'local', 'default' => 'logo.png']);
defined('POST_IMAGE_INFO') or define("POST_IMAGE_INFO", ['path' => 'upload/post/image', 'storage' => 'local', 'default' => 'logo.png']);
defined('EVENT_IMAGE_INFO') or define("EVENT_IMAGE_INFO", ['path' => 'upload/event/image', 'storage' => 'local', 'default' => 'event.jpg']);
defined('GROUP_IMAGE_INFO') or define("GROUP_IMAGE_INFO", ['path' => 'upload/group/image', 'storage' => 'local', 'default' => 'logo.png']);
defined('CHAT_IMAGE_INFO') or define("CHAT_IMAGE_INFO", ['path' => 'upload/chat', 'storage' => 's3', 'default' => 'user.png']);


// OTP Verification 
defined('OTP_LENGHT') or define("OTP_LENGHT", 4);
defined('OTP_RETRY_ATTEMPTS') or define("OTP_RETRY_ATTEMPTS", 3);
defined('OTP_RESEND_TIME') or define("OTP_RESEND_TIME", 60); // SECONDS
defined('OTP_EXPIRE_TIME') or define("OTP_EXPIRE_TIME", 300); // SECONDS
defined('RESET_PASSWORD_EXPIRE_TIME') or define("RESET_PASSWORD_EXPIRE_TIME", 900); // SECONDS


// Forgot email
defined('FORGOT_EMAIL_RESEND_TIME') or define("FORGOT_EMAIL_RESEND_TIME", 120); // SECONDS
defined('FORGOT_EMAIL_EXPIRE_TIME') or define("FORGOT_EMAIL_EXPIRE_TIME", 86400); // SECONDS
