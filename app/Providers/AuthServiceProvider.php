<?php

namespace App\Providers;

use App\Extensions\EmailHashUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('email_hash', function ($app, array $config) {
            return new EmailHashUserProvider();
        });
    }
}
