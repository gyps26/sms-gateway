<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | APK Path
    |--------------------------------------------------------------------------
    |
    | The path to the APK file for the application.
    |
    */

    'apk_path' => env('APK_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Logo Path
    |--------------------------------------------------------------------------
    |
    | The path to the logo image for the application.
    |
    */

    'logo_path' => env('APP_LOGO_PATH'),

    /*
    |--------------------------------------------------------------------------
    | License Code
    |--------------------------------------------------------------------------
    |
    | The license code for the application.
    |
    */

    'license_code' => env('APP_LICENSE_CODE', 'Unlicensed'),

    'announcements' => [

        /*
        |--------------------------------------------------------------------------
        | Guest Announcement
        |--------------------------------------------------------------------------
        |
        | The announcement message to be shown on landing page for guest users.
        |
        */

        'guest' => env('GUEST_ANNOUNCEMENT'),

        /*
        |--------------------------------------------------------------------------
        | Member Announcement
        |--------------------------------------------------------------------------
        |
        | The announcement message to be shown on dashboard for logged-in users.
        |
        */

        'member' => env('MEMBER_ANNOUNCEMENT'),
    ],

    'dashboard' => [

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        |
        | The stats to be shown on the dashboard.
        |
        */

        'stats' => [
            'campaigns' => false,
            'calls' => false,
            'messages' => true,
            'ussd_pulls' => true
        ],

        /*
        |--------------------------------------------------------------------------
        | Realtime
        |--------------------------------------------------------------------------
        |
        | When enabled, the dashboard will update in real-time every 10 seconds.
        |
        */

        'realtime' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    |
    | Enable or disable user registration.
    |
    */

    'registration' => false,

    /*
    |--------------------------------------------------------------------------
    | Home Page
    |--------------------------------------------------------------------------
    |
    | Show or hide the landing page.
    |
    */

    'homepage' => false,

    /*
    |--------------------------------------------------------------------------
    | Support URL
    |--------------------------------------------------------------------------
    |
    | The support URL for the application.
    |
    */

    'support_url' => env('APP_SUPPORT_URL', 'https://support.rbsoft.org'),

    /*
    |--------------------------------------------------------------------------
    | Version Code
    |--------------------------------------------------------------------------
    |
    | The version code of the application.
    |
    */

    'version_code' => 103,

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | The version of the application.
    |
    */

    'version' => '10.0.0-b.4'
];
