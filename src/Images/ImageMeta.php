<?php

namespace MasterDmx\LaravelMedia\Images;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use MasterDmx\LaravelMedia\BaseMeta;

class ImageMeta extends BaseMeta
{
    protected const ATTRIBUTE_TITLE = 'title';
    protected const ATTRIBUTE_WIDTH = 'width';
    protected const ATTRIBUTE_HEIGHT = 'height';

    /**
     * Заголовок
     *
     * @var string
     */
    public string $title;

    /**
     * Ширина изображения
     *
     * @var string
     */
    public string $width;

    /**
     * Высота изображения
     *
     * @var string
     */
    public string $height;

    /**
     * @param int         $width
     * @param int         $height
     * @param string      $title
     * @param string      $name
     * @param string      $mime
     * @param Carbon|null $uploadedAt
     */
    public function __construct(int $width, int $height, string $title, string $name, string $mime, Carbon $uploadedAt)
    {
        parent::__construct($name, $mime, $uploadedAt);

        $this->width = $width;
        $this->height = $height;
        $this->title = $title;
    }

    /**
     * Импорт из массива
     *
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): ImageMeta
    {
        return new static(
            $data[static::ATTRIBUTE_WIDTH] ?? 0,
            $data[static::ATTRIBUTE_HEIGHT] ?? 0,
            $data[static::ATTRIBUTE_TITLE] ?? '',
            $data[static::ATTRIBUTE_NAME] ?? 'Без названия',
            $data[static::ATTRIBUTE_MIME] ?? '',
            Carbon::parse($data[static::ATTRIBUTE_UPLOADED_AT] ?? '')
        );
    }

    /**
     * Экспорт в массив
     *
     * @return array
     */
    public function toArray(): array
    {
        return parent::toArray() + [
            static::ATTRIBUTE_TITLE => $this->title,
            static::ATTRIBUTE_WIDTH => $this->width,
            static::ATTRIBUTE_HEIGHT => $this->height,
        ];
    }
}
