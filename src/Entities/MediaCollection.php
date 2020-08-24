<?php

namespace MasterDmx\LaravelMedia\Entities;

use Illuminate\Support\Collection;

class MediaCollection extends Collection
{
    public function toArray(): array
    {
        return $this->map(function ($elem) {
            return $elem->toArray();
        })->all();
    }

    public function export(): array
    {
        $result = [];

        foreach ($this->items ?? [] as $key => $item) {
            $result[$item->id] = $item->export();
        }

        return $result;
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return value($default);
    }

    /**
     * Показ
     *
     * @param [type] $key
     * @param [type] $template
     * @param [type] $default
     * @return void
     */
    public function show($key, $template, $default = '')
    {
        return $this->has($key) ? $this->items[$key]->show($template) : value($default);
    }



}
