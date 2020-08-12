### Плагин для загрузки и использования файлов

1. Скачивание плагина
```php
$ composer require masterdmx/laravel-media
```

2. Подключение js зависимостей для фронта менеджера
```console
$ npm i vue-js-modal
```
```console
$ npm i vue-progressbar
```

3. Подключение сервис провайдера в config/app.php
```php
MasterDmx\LaravelMedia\MediaServiceProvider::class,
```

4. Публикация ресурсов и миграций
```console
php artisan vendor:publish
```

5. Запуск миграции
```console
php artisan migrate
```

6. Добавление диска в config/filesystem.php disks
```php
'media' => [
    'driver' => 'local',
    'root' => storage_path('app/public/media'),
    'url' => env('APP_URL').'/storage/media',
    'visibility' => 'public',
],
```

7. Подключение фронта
```js
import VueLaravelMediaManager from './vendor/media-manager/plugin.js';
Vue.use(VueLaravelMediaManager)
```
