<?php

namespace WebFuelAgency\UserAuthentication;

use Illuminate\Support\ServiceProvider;
use WebFuelAgency\UserAuthentication\Console\InstallCommand;

class UserAuthenticationServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [InstallCommand::class];
    }
}