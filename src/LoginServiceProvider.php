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
        $this->loadRoutesFrom(__DIR__ . '/routes/login.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'login');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/config/login.php', 'login');

        LoginResource::observe(LoginObserver::class);

        $this->publishes([
           __DIR__ . '/app/User.php' => app_path('User.php'),
        ], 'login.user');

        $this->publishes([
           __DIR__ . '/Http/Controllers/Auth/LoginController.php' => app_path('Http/Controllers/Auth/LoginController.php'),
        ], 'login.controller');
    }

    public function register()
    {
        Event::listen(Quill::MODULE, RegisterLoginModule::class);
        Event::listen(Quill::PERMISSION, RegisterLoginPermissionModule::class);
    }

    public function loadModuleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([

            ]);
        }
    }
}
