### Установка

1. Скачивание плагина
```
composer require masterdmx/laravel-media
```

2. Создание ссылки
```
php artisan storage:link
```

3. Подключение провайдера в config app.php
```php
'providers' => [
    MasterDmx\LaravelMedia\MediaServiceProvider::class,
]
```

4. Публикация конфига
```
php artisan vendor:publish --provider="MasterDmx\LaravelMedia\MediaServiceProvider" --tag="config"
```
