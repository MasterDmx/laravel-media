<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Support\ServiceProvider;
use MasterDmx\LaravelMedia\Contexts\ContextRegistry;

class MediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/media.php' => config_path('media.php')
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/media.php', 'media');

        // Добавляем диск в диски ларавел
        config(['filesystems.disks.' . config('media.disk') => config('media.disk_settings')]);

        // Привязываем синглтон реестра контекстов
        $this->app->singleton(ContextRegistry::class, fn () => (new ContextRegistry())->add(config('media.default_context'))->addFromArray(config('media.contexts')));

        // Привязываем синглтон сервис-класса
        $this->app->singleton(MediaService::class);

        // Привязываем синглтон менеджера
        $this->app->singleton(MediaManager::class);
    }
}
