<?php

namespace MasterDmx\LaravelMedia;

class MediaConfig
{
    const DEFAULT_DISK      = 'media';
    const DEFAULT_USER_MODE = false;
    const DEFAULT_TYPE      = 'file';

    public function disk(): string
    {
        return config('media.disk', static::DEFAULT_DISK);
    }

    public function userMode(): bool
    {
        return config('media.user_mode', static::DEFAULT_USER_MODE);
    }

    public function defaultType(): bool
    {
        return config('default_type', static::DEFAULT_TYPE);
    }

    public function types(): array
    {
        return config('types', static::DEFAULT_TYPE);
    }
}
