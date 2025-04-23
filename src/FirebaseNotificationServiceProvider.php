<?php

namespace ZeinApi\FirebaseNotification;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FirebaseNotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/firebase-notification.php',
            'firebase-notification'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/firebase-notification.php' => config_path('firebase-notification.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'firebase-notification');
    }
}