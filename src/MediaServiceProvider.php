<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Support\ServiceProvider;
use MasterDmx\LaravelMedia\Models\Media;
use MasterDmx\LaravelMedia\Services\Uploader;

class MediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
            __DIR__.'/../resources/js' => resource_path('js/vendor/media-manager'),
            __DIR__.'/../resources/sass' => resource_path('sass/vendor/media-manager'),
        ], 'media');
    }


    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/media.php', 'media');

        $this->app->singleton(MediaManager::class, function () {
            return new MediaManager(
                $this->app->make(Uploader::class),
                $this->app->make(Media::class)
            );
        });
    }
}
