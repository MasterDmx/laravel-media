<?php

return [

    // Контексты
    'contexts' => [],

    'default_context' => \MasterDmx\LaravelMedia\Contexts\DefaultContext::class,

    // Соответствие MIME типов к расширению файлов
    'extensions' => [
        'image/gif'     => 'gif',
        'image/jpeg'    => 'jpeg',
        'image/png'     => 'png',
        'image/svg+xml' => 'svg',
        'image/webp'    => 'webp',
    ],

    // Идентификатор диска
    'disk' => 'images',

    // Параметры диска
    'disk_settings' => [
        'driver' => 'local',
        'root' => storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'media'),
        'url' => env('APP_URL').'/storage/media',
        'visibility' => 'public',
    ],

    // Название оригинального файла после загрузки (без расширения)
    'default_file_name' => 'default',

    // Название МЕТА файла (без расширения)
    'meta_file_name' => 'meta',

    // Тексты ошибок при валидации
    'validation_messages' => [
        'min_width' => 'Минимальная ширина изображения должна быть {rule_min_width}px, текущая: {width}px',
        'max_width' => 'Максимальная ширина изображения должна быть {rule_max_width}px, текущая: {width}px',
        'min_height' => 'Минимальная высота изображения должна быть {rule_min_height}px, текущая: {height}px',
        'max_height' => 'Максимальная высота изображения должна быть {rule_max_height}px, текущая: {height}px',
    ],
];
