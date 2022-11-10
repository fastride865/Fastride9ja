<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'daily'),
    'log_max_files' => 5,

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

//        'single' => [
//            'driver' => 'daily',
//            'path' => storage_path('logs/laravel.log'),
//            'level' => 'debug',
//        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 1,
        ],
//        'slack' => [
//            'driver' => 'slack',
//            'url' => env('LOG_SLACK_WEBHOOK_URL'),
//            'username' => 'Laravel Log',
//            'emoji' => ':boom:',
//            'level' => 'critical',
//        ],
//        'stderr' => [
//            'driver' => 'monolog',
//            'handler' => StreamHandler::class,
//            'with' => [
//                'stream' => 'php://stderr',
//            ],
//        ],
//        'syslog' => [
//            'driver' => 'syslog',
//            'level' => 'debug',
//        ],
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
//        'expections' => [
//            'driver' => 'daily',
//            'path' => storage_path('logs/error.log'),
//            'level' => 'debug',
//            'days' => 3,
//        ],
        'onesignal' => [
            'driver' => 'daily',
            'path' => storage_path('logs/onesignal.log'),
            'level' => 'debug',
            'days' => 3,
        ],
        'booking' => [
            'driver' => 'daily',
            'path' => storage_path('logs/booking.log'),
            'level' => 'debug',
            'days' => 3,
        ],
        'google_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/google_api.log'),
            'level' => 'debug',
            'days' => 3,
        ],
        'mpessa_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mpessa_api.log'),
            'level' => 'debug',
        ],
        'paygate_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/paygate_api.log'),
            'level' => 'debug',
        ],
        'payphone_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payphone_api.log'),
            'level' => 'debug',
        ],
        'aamarpay_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/aamarpay_api.log'),
            'level' => 'debug',
        ],
        'payfast_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payfast_api.log'),
            'level' => 'debug',
        ],
        'paybox_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/paybox_api.log'),
            'level' => 'debug',
            'days' => 15,
        ],
        'payhere_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payhere_api.log'),
            'level' => 'debug',
        ],
        'mercadocard_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mercadocard_api.log'),
            'level' => 'debug',
        ],

        'mercadopix_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mercadopix_api.log'),
            'level' => 'debug',
        ],
        'beyonic' => [
            'driver' => 'daily',
            'path' => storage_path('logs/beyonic.log'),
            'level' => 'debug',
        ],
         
        'paygate_global_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/paygate_global_api.log'),
            'level' => 'debug',
        ],
        'dpo_think_payment_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dpo_think_payment_api.log'),
            'level' => 'debug',
        ],
        'touch_pay_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/touch_pay_api.log'),
            'level' => 'debug',
        ],

       'flo_payment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/flo_payment.log'),
            'level' => 'debug',
            'days' => 15,
        ],

        'maxi_cash' => [
            'driver' => 'daily',
            'path' => storage_path('logs/maxi_cash.log'),
            'level' => 'debug',
            'days' => 7,
        ],
        'whatsapp_booking' => [
            'driver' => 'daily',
            'path' => storage_path('logs/whatsapp_booking.log'),
            'level' => 'debug',
            'days' => 1,
        ],
        'maillog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/maillog.log'),
            'level' => 'debug',
            'days' => 2,
        ],
        'referral_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/referral_log.log'),
            'level' => 'debug',
            'days' => 2,
        ],
        'per_day_cron_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/per_day_cron_log.log'),
            'level' => 'debug',
            'days' => 2,
        ],
        'per_minute_cron_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/per_minute_cron_log.log'),
            'level' => 'debug',
            'days' => 2,
        ],
        'wave_business' => [
            'driver' => 'daily',
            'path' => storage_path('logs/wave_business.log'),
            'level' => 'debug',
            'days' => 2,
        ],
        'teliberrPay' => [
            'driver' => 'daily',
            'path' => storage_path('logs/teliberrPay.log'),
            'level' => 'debug',
        ],
    ],
];
