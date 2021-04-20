<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Персональное хранилища медиа сущности
 *
 * @package MasterDmx\LaravelMedia
 */
class MediaStorage
{
    /**
     * Обозначение
     *
     * @var Id
     */
    private Id $id;

    /**
     * Laravel файловая система
     *
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * Локальный путь каталогов в рамках laravel диска
     *
     * @var string
     */
    private string $localPath;

    /**
     * Локальный url каталогов в рамках laravel диска
     *
     * @var string
     */
    private string $localUrl;

    /**
     * Storage constructor.
     *
     * @param Id $id
     */
    public function __construct(Id $id)
    {
        $this->id = $id;
        $this->filesystem = static::getDisk();

        $catalogs = [
            $this->id->getYear(),
            $this->id->getMonth(),
            substr($this->id->getHash(), 0, 1),
            $this->id->toString()
        ];

        $this->localPath = implode(DIRECTORY_SEPARATOR, $catalogs);
        $this->localUrl = implode('/', $catalogs);
    }

    /**
     * Создает объект хранилища
     *
     * @param Id $id
     *
     * @return MediaStorage
     */
    public static function init(Id $id): MediaStorage
    {
        return new static($id);
    }

    /**
     * Получить объект Laravel диска
     *
     * @return Filesystem
     */
    public static function getDisk(): Filesystem
    {
        return Storage::disk(config('media.disk'));
    }

    /**
     * Записать данные в файл
     *
     * @param string $path
     * @param        $content
     * @param array  $options
     *
     * @return bool
     */
    public function put(string $path, $content, $options = []): bool
    {
        return $this->filesystem->put($this->getLocalPathTo($path), $content, $options);
    }

    /**
     * Получает содержимое файла
     *
     * @param string $path
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get(string $path): string
    {
        return $this->filesystem->get($this->getLocalPathTo($path));
    }

    /**
     * Получить путь до персонального хранилища
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->filesystem->path($this->localPath);
    }

    /**
     * Получить путь до вложенного каталоага \ файла
     *
     * @param string $path
     *
     * @return string
     */
    public function getPathTo(string $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Получить путь до оригинального файла
     *
     * @return string
     */
    public function getPathToFile(): string
    {
        return $this->getPathTo($this->getFileFullName());
    }

    /**
     * Получить путь до файла или каталога, относительно диска
     *
     * @param string $path
     *
     * @return string
     */
    public function getLocalPathTo(string $path): string
    {
        return $this->localPath . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Получить URL до оригинального файла
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getUrlTo($this->getFileFullName());
    }

    /**
     * Получить URL до файла
     *
     * @param $url
     *
     * @return string
     */
    public function getUrlTo($url): string
    {
        return $this->filesystem->url($this->localUrl . '/' . $url);
    }

    /**
     * Проверяет существование хранилища
     *
     * @return bool
     */
    public function checkExists(): bool
    {
        return file_exists($this->getPath());
    }

    /**
     * Проверяет существование хранилища
     *
     * @param string $path
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        return file_exists($this->getPathTo($path));
    }

    /**
     * Проверяет существование оригинального файла
     *
     * @return bool
     */
    public function hasDefaultFile(): bool
    {
        return file_exists($this->getPathToFile());
    }

    /**
     * Создает структуру папок хранилища на диске
     *
     * @return bool
     */
    public function createStorage(): bool
    {
        return mkdir($this->getPath(), 0777, true);
    }

    /**
     * Создает директории внутри хранилища
     *
     * @param string $path
     *
     * @return bool
     */
    public function createDirectory(string $path): bool
    {
        return mkdir($this->getPathTo($path), 0777, true);
    }

    /**
     * Создает вложенные каталоги, если их нет
     *
     * @param $path
     *
     * @return bool
     */
    public function createDirectoryIfNotHas($path): bool
    {
        if ($this->has($path)) {
            return true;
        }

        return $this->createDirectory($path);
    }

    /**
     * Получить название оригинального файла
     *
     * @return string
     */
    public function getFileName(): string
    {
        return config('media.default_file_name');
    }

    /**
     * Получить полное название оригинального файла
     *
     * @return string
     */
    public function getFileFullName(): string
    {
        return $this->getFileName() . '.' . $this->id->getExtension();
    }

    /**
     * Удаляет медиа
     *
     * @return bool
     */
    public function remove(): bool
    {
        return $this->filesystem->deleteDirectory($this->localPath);
    }

    /**
     * Удаляет определенный файл в медиа каталоге
     *
     * @param string $path
     *
     * @return bool
     */
    public function removeFile(string $path): bool
    {
        return $this->filesystem->delete($this->getLocalPathTo($path));
    }

    /**
     * Получить основное расширение файла
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->id->getExtension();
    }

    /**
     * Возвращает все файлы в корневой медиа директории
     */
    public function getFiles(): array
    {
        $files = [];

        foreach (scandir($this->getPath()) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $files[] = $item;
        }

        return $files;
    }

    /**
     * Возвращает все файлы в доп. каталогах медиа директории
     *
     * @param string $path
     *
     * @return array
     */
    public function getFilesIn(string $path): array
    {
        $files = [];

        if ($this->has($path)) {
            foreach (scandir($this->getPathTo($path)) as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $files[] = $item;
            }
        }

        return $files;
    }
}
