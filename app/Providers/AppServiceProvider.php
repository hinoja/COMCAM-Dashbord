<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;
use Filament\Support\Assets\Js;

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
        Model::preventLazyLoading();
        // Filament\Support\Facades\FilamentAsset::register([
        //     // Local asset build using Vite
        //     Js::make('sweetalert2', Vite::asset('resources/js/sweetalert2.js')),
        //     Js::make('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11'),
        //    ]);
    }
}
