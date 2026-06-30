<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Añadimos esta importación para poder usar el comando de HTTPS

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Fuerza a Laravel a generar todos los enlaces de CSS, JS e imágenes con HTTPS en Render
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // 2. Tu código original para la configuración de las vistas (se mantiene intacto)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $config = null;
            try {
                $config = \App\Models\Configuracion::first();
            } catch (\Throwable $e) {
                // Table might not exist yet during migrations or tests setup
            }
            $view->with('config', $config ?? new \App\Models\Configuracion());
        });
    }
}
