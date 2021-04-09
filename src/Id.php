<?php

namespace MasterDmx\LaravelMedia;

use MasterDmx\LaravelMedia\Exceptions\IncorrectIdException;
use phpDocumentor\Reflection\Types\This;

class Id
{
    /**
     * Год
     *
     * @var string
     */
    private string $year;

    /**
     * Месяц
     *
     * @var string
     */
    private string $month;

    /**
     * Хэш
     *
     * @var string
     */
    private string $hash;

    /**
     * Расширение оригинального файла
     *
     * @var string
     */
    private string $extension;

    public function __construct(string $year, string $month, string $hash, string $extension)
    {
        $this->year      = $year;
        $this->month     = $month;
        $this->hash      = $hash;
        $this->extension = $extension;
    }

    /**
     * Генерирует новый Алиас на основе текущего года, хэша и расширения файла
     *
     * @param string $extension
     *
     * @return static
     */
    public static function generate(string $extension): self
    {
        return new Id(date('Y'), date('m'), substr(md5(microtime() . rand(0, 1000)), 0, 15) . rand(0, 9) . date('is'), $extension);
    }

    /**
     * Преобразует строковое обозначение в объект
     *
     * @param string $alias
     *
     * @return static
     */
    public static function parse(string $alias): self
    {
        $parsed = explode( '-', $alias);

        if (!isset($parsed[1])) {
            throw new IncorrectIdException("Incorrect file alias: {$alias}. No extension specified");
        }

        return new Id(
            substr($parsed[0], 0, 4),
            substr($parsed[0], 4, 2),
            substr($parsed[0], 6),
            $parsed[1]
        );
    }

    /**
     * Строковое обозначение
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->year . $this->month . $this->hash . '-' . $this->extension;
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
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
