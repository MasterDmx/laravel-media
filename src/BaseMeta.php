<?php

namespace MasterDmx\LaravelMedia;

use Carbon\Carbon;

abstract class BaseMeta implements Meta
{
    protected const ATTRIBUTE_NAME        = 'name';
    protected const ATTRIBUTE_MIME        = 'mime';
    protected const ATTRIBUTE_UPLOADED_AT = 'uploaded_at';

    /**
     * Название
     *
     * @var string
     */
    public string $name;

    /**
     * MIME тип
     *
     * @var string
     */
    public string $mime;

    /**
     * Дата загрузки
     *
     * @var Carbon
     */
    public Carbon $uploadedAt;

    /**
     * BaseMeta constructor.
     *
     * @param string $name
     * @param string $mime
     * @param Carbon $uploadedAt
     */
    public function __construct(string $name, string $mime, Carbon $uploadedAt)
    {
        $this->name       = $name;
        $this->mime       = $mime;
        $this->uploadedAt = $uploadedAt;
    }

    /**
     * Импорт из массива
     *
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): BaseMeta
    {
        return new static(
            $data[static::ATTRIBUTE_NAME] ?? '',
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
        return [
            static::ATTRIBUTE_NAME => $this->name,
            static::ATTRIBUTE_MIME => $this->mime,
            static::ATTRIBUTE_UPLOADED_AT => $this->uploadedAt->toString(),
        ];
    }
}
