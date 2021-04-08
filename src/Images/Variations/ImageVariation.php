<?php

namespace MasterDmx\LaravelMedia\Images\Variations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class ImageVariation implements Arrayable, Jsonable
{
    /**
     * Название вариации
     *
     * @var string
     */
    private string $name;

    /**
     * Расширение
     *
     * @var string
     */
    private string $extension;

    /**
     * URL
     *
     * @var string
     */
    private string $url;

    /**
     * ImageVariation constructor.
     *
     * @param string $name
     * @param string $extension
     * @param string $url
     */
    public function __construct(string $name, string $extension, string $url)
    {
        $this->name = $name;
        $this->extension = $extension;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Отобразить в качестве массива
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->getName(),
            'extension'   => $this->getExtension(),
            'url'         => $this->getUrl(),
        ];
    }

    /**
     * Отобразить в качестве JSON
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);;
    }
}
