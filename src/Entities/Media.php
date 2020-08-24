<?php

namespace MasterDmx\LaravelMedia\Entities;

use MasterDmx\LaravelMedia\MediaHelper;

abstract class Media
{
    /**
     * Идентификатор медиа контента
     *
     * @var string
     */
    public $id;

    /**
     * Путь
     *
     * @var string
     */
    public $path;

    /**
     * Идентификатор медиа-типа
     *
     * @var string
     */
    public $type;

    abstract public static function import(array $data);

    public function __construct(string $id, string $path, string $type)
    {
        $this->id = $id;
        $this->path = $path;
        $this->type = $type;
    }

    public function getUrl()
    {
        return MediaHelper::getUrl($this->path);
    }

    /**
     * Экспорт данных для хранения
     *
     * @return array
     */
    public function export(): array
    {
        $data = [
            'path' => $this->path
        ];

        if ($this->type !== 'file') {
            $data['type'] = $this->type;
        }

        return $data;
    }

    public function getExportKey()
    {
        return $this->type . '/' . $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Показ
     *
     * @param string $template
     * @return string
     */
    public function show(string $template): string
    {
        $template = str_replace('{id}', $this->id, $template);
        $template = str_replace('{path}', $this->path, $template);
        $template = str_replace('{url}', $this->getUrl(), $template);

        return $template;
    }
}
