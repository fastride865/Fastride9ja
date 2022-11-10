<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        
        'App\Events\WebPushNotificationEvent' => [
            'App\Listeners\WebPushNotificationListener',
        ],
        
        'App\Events\RideBookingDriverNotificationEvent' => [
            'App\Listeners\RideBookingDriverNotificationListener',
        ],

        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\PassportTokennewCreated',
        ],
        'App\Events\UserSignupWelcome' => [
            'App\Listeners\UserSignupWelcomeListener',
        ],
        
        'App\Events\UserSignupEmailOtpEvent' => [
            'App\Listeners\UserSignupEmailOtpListener',
        ],

        'App\Events\UserForgotPasswordEmailOtpEvent' => [
            'App\Listeners\UserForgotPasswordEmailOtpListener',
        ],

        'App\Events\CustomerSupportEvent' => [
            'App\Listeners\CustomerSupportListener',
        ],
        'App\Events\SendUserInvoiceMailEvent' => [
            'App\Listeners\SendUserInvoiceMailListener',
        ],
        
        'App\Events\DriverSignupEmailOtpEvent' => [
            'App\Listeners\DriverSignupEmailOtpListener',
        ],
        'App\Events\DriverForgotPasswordEmailOtpEvent' => [
            'App\Listeners\DriverForgotPasswordEmailOtpListener',
        ],
        'App\Events\DriverSignupWelcome' => [
            'App\Listeners\DriverSignupWelcomeListener',
        ],
        'App\Events\MailProcessedEvent' => [
            'App\Listeners\MailProcessedListener',
        ],
        'App\Events\SendUserHandymanInvoiceMailEvent' => [
            'App\Listeners\SendUserHandymanInvoiceMailListener',
        ],
        'App\Events\SendNewOrderRequestMailEvent' => [
            'App\Listeners\SendNewOrderRequestMailListener',
        ],
        'App\Events\SendNewRideRequestMailEvent' => [
            'App\Listeners\SendNewRideRequestMailListener',
        ],
        'App\Events\BusinessSegmentForgotPasswordEmailOtpEvent' => [
            'App\Listeners\BusinessSegmentForgotPasswordEmailOtpListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
