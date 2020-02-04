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

        // $this->publishes([
        //     __DIR__ . '/config/login.php' => config_path('login.php'),
        // ], 'logins.config');

        // $this->publishes([
        //    __DIR__ . '/views' => resource_path('/vendor/login'),
        // ], 'logins.views');

        $this->publishes([
        	__DIR__ . '/database/factories/LoginFactory.php' => database_path('factories/LoginFactory.php'),
            __DIR__ . '/database/seeds/LoginTableSeeder.php' => database_path('seeds/LoginTableSeeder.php'),
        ], 'logins.migration');
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
