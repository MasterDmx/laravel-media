<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Support\ServiceProvider as LeraverServiceProvider;
use MasterDmx\LaravelMedia\Models\Media;
use MasterDmx\LaravelMedia\Services\Uploader;

class ServiceProvider extends LeraverServiceProvider
{
    public function boot()
    {
        $this->publishes([ __DIR__.'/../migrations' => database_path('migrations')], 'media');
    }

    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/media.php', 'media');

        // Добавляем диск
        config(['filesystems.disks.' . config('media.disk', 'media') => config('media.disk_settings')]);

        $this->app->singleton(MediaManager::class, function () {
            return new MediaManager(
                $this->app->make(Uploader::class),
                $this->app->make(Media::class)
            );
        });
    }
}
