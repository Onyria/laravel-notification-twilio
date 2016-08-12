<?php

namespace NotificationChannels\Twilio;

use Illuminate\Support\ServiceProvider;
use Services_Twilio as Twilio;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(Channel::class)
            ->needs(Twilio::class)
            ->give(function () {
                $twilioConfig = config('services.twilio');

                return new Twilio(
                    $twilioConfig['sid'],
                    $twilioConfig['token']
                );
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
