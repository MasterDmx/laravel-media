<?php

namespace MasterDmx\LaravelMedia\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['path', 'type', 'name', 'uploaded_user_id', 'uploaded_at', 'imported_to'];

    /**
     * Добавить запись
     *
     * @param string $path
     * @param string $type
     * @param string $name
     * @param [type] $uploadedUserId
     * @param string $importedTo
     */
    public function add(string $path, string $type, string $name = null, $uploadedUserId = null, string $importedTo = null)
    {
        return static::create([
            'path' => $path,
            'type' => $type,
            'name' => $name ?? '',
            'uploaded_user_id' => $uploadedUserId ?? 0,
            'imported_to' => $importedTo ?? '',
        ]);
    }

    // --------------------------------------------------------------------
    // Scopes
    // --------------------------------------------------------------------

    public function scopeImportedTo($q, string $to)
    {
        return $q->where('imported_to', $to);
    }
}
