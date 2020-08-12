<?php

return [
    /**
     * Идентификатор диска
     */
    'disk' => 'media',

    'manager_route_preffix' => 'vendor/media/manager',

    /**
     * Загружаемые расширения
     */
    'allow_extensions' => [
        'png', 'jpeg', 'jpg', 'ico', 'gif', 'svg',
        'word', 'pdf',
    ],

    /**
     * Разрешить использование пользователей
     */
    'user_mode' => false,

    'default_entity' => \MasterDmx\LaravelMediaManager\Entities\FileMedia::class,

    /**
     * Определения типов
     */
    'types' => [
        'image' => [
            'driver' => 'image',
            'extensions' => ['png', 'jpeg', 'jpg', 'ico', 'gif', 'svg'],
            'entity' => \MasterDmx\LaravelMediaManager\Entities\ImageMedia::class,
        ],

        'document' => [
            'extensions' => ['png', 'jpeg', 'jpg', 'ico', 'gif', 'svg'],
        ],
    ],
];
