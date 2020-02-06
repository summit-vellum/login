<?php

namespace Quill\Login;

use Vellum\Module\Quill;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Quill\Login\Listeners\RegisterLoginModule;
use Quill\Login\Listeners\RegisterLoginPermissionModule;
use Quill\Login\Resource\LoginResource;
use Quill\Login\Models\LoginObserver;

class LoginServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadModuleCommands();

        $this->publishes([
           __DIR__ . '/app/User.php' => app_path('User.php'),
        ], 'login.user');

        $this->publishes([
           __DIR__ . '/Http/Controllers/Auth/LoginController.php' => app_path('Http/Controllers/Auth/LoginController.php'),
        ], 'login.controller');
    }

    public function register()
    {

    }

    public function loadModuleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([

            ]);
        }
    }
}
