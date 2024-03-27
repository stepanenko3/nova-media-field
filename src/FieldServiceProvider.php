<?php

namespace Stepanenko3\NovaMediaField;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Route;
use Stepanenko3\NovaMediaField\Http\Controllers\RegenerateController;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(
            path: __DIR__ . '/../lang',
            namespace: 'nova-media-field',
        );

        $this->loadJsonTranslationsFrom(
            path: __DIR__ . '/../lang',
        );

        Nova::serving(function (ServingNova $event): void {
            Nova::script('nova-media-field', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-media-field', __DIR__ . '/../dist/css/field.css');
            Nova::translations(__DIR__ . '/../lang/' . app()->getLocale() . '.json');
        });

        $this->app->booted(function (): void {
            $this->routes();
        });
    }

    public function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/stepanenko3/nova-media-field')
            ->group(function (): void {
                // Route::post('sort', Http\Controllers\SortController::class);
                // Route::post('{media}/crop', Http\Controllers\CropController::class);
                Route::post('{id}/regenerate', RegenerateController::class);
                // Route::get('{resource}/{resourceId}/media/{field}', Http\Controllers\IndexController::class);
                // Route::get('{resource}/{resourceId}/media/{field}/attachable', Http\Controllers\AttachableController::class);
                // Route::post('{resource}/{resourceId}/media/{field}', Http\Controllers\AttachController::class);
            });
    }

    public function register(): void
    {
        //
    }
}
