<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase credentials JSON file in storage
    |
    */
    'credentials_path' => env('FIREBASE_CREDENTIALS'),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification.
    |
    */
    'app_name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Icon
    |--------------------------------------------------------------------------
    |
    | This value is the URL to your application's icon. This value is used when
    | sending notifications that require an icon.
    |
    */
    'app_icon' => env('APP_URL') . '/imgs/logo.svg',

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | This is a list of languages that your application supports for notifications.
    |
    */
    'supported_languages' => ['en'],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This is the model class for the user model in your application.
    |
    */
    'user_model' => 'App\\Models\\User',

    /*
    |--------------------------------------------------------------------------
    | FCM Token Model
    |--------------------------------------------------------------------------
    |
    | This is the model class for the FCM token model in your application.
    |
    */
    'fcm_token_model' => 'Evexel\\FirebaseNotification\\Models\\FCMToken',
];