<?php

namespace MasterDmx\LaravelMedia\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['path', 'type', 'name', 'uploaded_user_id', 'uploaded_at'];

    public static function add(string $path, string $type, string $name, $uploadedUserId = null)
    {
        return static::create([
            'path' => $path,
            'type' => $type,
            'name' => $name,
            'uploaded_user_id' => $uploadedUserId ?? 0
        ]);
    }
}
