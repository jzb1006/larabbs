<?php

namespace App\Providers;

use App\Listeners\PushNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Weixin\WeixinExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \Illuminate\Auth\Events\Verified::class=>[
            \App\Listeners\EmailVerifed::class,
        ],
        SocialiteWasCalled::class=>[
            'SocialiteProviders\Weixin\WeixinExtendSocialite@handle'
        ],
//        'eloquent.created'
       'eloquent.created:DatabaseNotification::class'=>[
             PushNotification::class
       ]
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
