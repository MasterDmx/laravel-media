<?php

namespace MasterDmx\LaravelMedia\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use MasterDmx\LaravelMedia\MediaHelper;
use MasterDmx\LaravelMedia\Models\Media;
use MasterDmx\LaravelMedia\Services\Uploader;
use Storage;

class ManagerController
{
    /**
     * Инициализация
     */
    public function init(Request $request)
    {
        return [
            'allow_extensions' => config('media.allow_extensions'),
            'storage_url' => MediaHelper::getUrl(''),
            'user_mode' => config('media.user_mode'),
        ];
    }

    /**
     * Загрузка файлов
     */
    public function files(Request $request)
    {
        return Media::orderBy('id', 'DESC')->get()->each(function ($item) {
            $item->url = MediaHelper::getUrl($item->path);
        });
    }

    /**
     * Загрузка файла
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'image',
                'mimes:' . implode(',', config('media.allow_extensions'))
            ]
        ]);

        $uploader = new Uploader($request->file('file'));

        if ($uploader->upload($request->file('file'))) {
            $file = Media::add(
                $uploader->getUriPath(),
                $uploader->getType(),
                $uploader->getOriginalName(),
            );
            $file->url = MediaHelper::getUrl($file->path);

            return $file;
        }

        return response(['status' => 'error'], 400);
    }

    public function remove($id)
    {
        if ($media = Media::find($id)) {
            $media->delete();
            Storage::disk(config('media.disk'))->delete($media->path);

            return response(['status' => 'success'], 200);
        }

        return response(['status' => 'file_not_found'], 404);
    }

}
