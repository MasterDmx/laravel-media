<?php

namespace MasterDmx\LaravelMedia\Contexts;

use Illuminate\Container\Container;
use MasterDmx\LaravelMedia\Exceptions\UndefinedContextException;

class ContextRegistry
{
    /**
     * @var array|string[]
     */
    private array $contexts = [];

    /**
     * Добавить контекст
     *
     * @param string $class
     *
     * @return $this
     */
    public function add(string $class): ContextRegistry
    {
        $name = $class::name();
        $this->getContainer()->singleton($class);
        $this->contexts[$name] = $class;

        return $this;
    }

    /**
     * Добавить контексты из массива
     *
     * @param array $data
     *
     * @return $this
     */
    public function addFromArray(array $data): ContextRegistry
    {
        foreach ($data as $class) {
            $this->add($class);
        }

        return $this;
    }

    /**
     * Проверить наличие
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->contexts[$name]);
    }

    /**
     * Получить инстанс
     *
     * @param $name
     *
     * @return Context
     */
    public function get($name): Context
    {
        if (!$this->has($name)) {
            throw new UndefinedContextException('Undefined context "' . $name . '"');
        }

        return app($this->contexts[$name]);
    }

    /**
     * Получить класс по названию вариации
     *
     * @param string $name
     *
     * @return Context
     */
    public function getClass(string $name): Context
    {
        return $this->contexts[$name];
    }

    /**
     * Получить название вариации по классу
     *
     * @param $class
     *
     * @return string
     */
    public function getName($class): string
    {
    }

    /**
     * Получить контейнер
     *
     * @return Container
     */
    private function getContainer(): Container
    {
        return Container::getInstance();
    }
}
