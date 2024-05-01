<?php

namespace Thoughtco\StatamicPostmarkSpamcheck;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        \Statamic\Events\FormSubmitted::class => [Listeners\FormSubmittedListener::class],
    ];

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'statamic-postmark-spamcheck');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/statamic-postmark-spamcheck.php' => config_path('statamic-postmark-spamcheck.php'),
            ], 'statamic-postmark-spamcheck');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/statamic-postmark-spamcheck'),
            ], 'statamic-postmark-spamcheck-views');
        }
    }
}
