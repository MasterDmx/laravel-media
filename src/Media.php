<?php

namespace MasterDmx\LaravelMedia;

interface Media
{
    /**
     * Получить ID
     *
     * @return Id
     */
    public function getId(): Id;

    /**
     * Получить расширение
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Получить полный URL до базового файла
     *
     * @return string
     */
    public function getUrl(): string;

    public function export(): array;
}
