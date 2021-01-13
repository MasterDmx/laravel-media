<?php

return [
    /**
     * Идентификатор диска
     */
    'disk' => 'media',

    /**
     * Диск
     */
    'disk_settings' => [
        'driver' => 'local',
        'root' => storage_path('app/public/media'),
        'url' => env('APP_URL').'/storage/media',
        'visibility' => 'public',
    ],

    /**
     * Разрешить использование пользователей
     */
    'user_mode' => false,

    /**
     * Тип по умолчанию
     */
    'default_type' => 'file',

    /**
     * Определение типов
     * Ключ - идентификатор типа
     * Значение - массив расширений файлов
     * -- Если расшрение не найдено - будет установлен тип по умолчанию
     */
    'types' => [
        'image' => ['png', 'jpeg', 'jpg', 'ico', 'gif', 'svg'],
        'document' => ['pdf'],
    ],

    /**
     * Объекты-обработчики
     * * Ключ - идентификатор типа (default - обработчик по умолчанию)
     * * Значение - название класса
     */
    'handlers' => [
        'image' => \MasterDmx\LaravelMedia\Entities\Media\Image::class,
    ],
];
