<?php

namespace MasterDmx\LaravelMedia\Entities;

use MasterDmx\LaravelMedia\MediaHelper;

abstract class Media
{
    /**
     * Ключ файла в рамках коллекции
     *
     * @var string
     */
    public $key;

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

    /**
     * Файл
     *
     * @var \MasterDmx\LaravelMedia\Entities\File
     */
    public $file;

    abstract public static function instance(array $data);

    public function __construct(string $path, string $type, string $key = null)
    {
        $this->path = $path;
        $this->type = $type;
        $this->file = new File($path);

        if (isset($key)) {
            $this->key = $key;
        }
    }

    public function getUrl()
    {
        return $this->file->getUrl();
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
            'key' => $this->key,
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
    public function show(string $template = null): string
    {
        if (!isset($template)) {
            return '';
        }

        $template = str_replace('{key}', $this->key, $template);
        $template = str_replace('{path}', $this->path, $template);
        $template = str_replace('{url}', $this->getUrl(), $template);

        return $template;
    }
}
