<?php

namespace MasterDmx\LaravelMedia\Images;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use MasterDmx\LaravelMedia\Id;
use MasterDmx\LaravelMedia\Images\Variations\ImageVariationCollection;
use MasterDmx\LaravelMedia\Media;
use MasterDmx\LaravelMedia\MediaStorage;

class Image implements Media, Arrayable, Jsonable
{
    private Id $id;
    private MediaStorage $storage;
    private ImageMeta $meta;
    private ImageVariationCollection $variations;

    /**
     * Уникальный title \ alt аттрибут
     *
     * @var string|null
     */
    private ?string $personalTitle;

    /**
     * Image constructor.
     *
     * @param Id                       $id
     * @param MediaStorage             $storage
     * @param ImageMeta                $meta
     * @param ImageVariationCollection $variations
     * @param string|null              $title
     */
    public function __construct(Id $id, MediaStorage $storage, ImageMeta $meta, ImageVariationCollection $variations, string $title = null)
    {
        $this->id = $id;
        $this->storage = $storage;
        $this->meta = $meta;
        $this->variations = $variations;
        $this->personalTitle = $title;
    }

    /**
     * Получить объект обозначения
     *
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * Получить расширение файла
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->id->getExtension();
    }

    /**
     * Получить название
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->meta->name;
    }

    /**
     * Получить ширину
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->meta->width;
    }

    /**
     * Получить высоту
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->meta->height;
    }

    /**
     * Получить TITLE тэг
     *
     * @return string
     */
    public function getTitle(): string
    {
        return !empty($this->personalTitle) ? $this->personalTitle : $this->meta->title;
    }

    /**
     * Получить URL до оригинального файла
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->storage->getUrl();
    }

    /**
     * Дата \ время загрузки файла
     *
     * @return Carbon
     */
    public function uploadedAt(): Carbon
    {
        return $this->meta->uploadedAt;
    }

    /**
     * Получить МЕТА объект
     *
     * @return ImageMeta
     */
    public function getMeta(): ImageMeta
    {
        return $this->meta;
    }

    /**
     * Представление изображение в виде $template шаблона (если не указан будет использовано <img src=@ title=@ alt=@>)
     *
     * @param string|null $template
     *
     * @return string
     */
    public function performance(string $template = null): string
    {
        return $this->performanceBuild($this->getUrl(), $template);
    }

    // -------------------------------------------------------
    // Вариации
    // -------------------------------------------------------

    /**
     * Получить УРЛ вариации
     * Позволяет подать на вход массив потенциальный вариаций. Если первая будет не найдена - переход ко второй
     * Флаг $useDefault = true выведет дефолтное изображение, если вариаций не найдено
     *
     * @param string|array $variations
     * @param bool         $useDefault
     *
     * @return string|null
     */
    public function getUrlFor($variations, $useDefault = true): ?string
    {
        if (is_string($variations)) {
            $variations = explode(',', $variations);
        }

        foreach ($variations as $variation) {
            if ($this->variations->has($variation)) {
                return $this->variations->get($variation)->getUrl();
            }
        }

        return $useDefault ? $this->getUrl() : null;
    }

    /**
     * Представление вариации изображение в виде $template шаблона (если не указан будет использовано <img src=@ title=@ alt=@>)
     *
     * @param             $variations
     * @param bool        $useDefault
     * @param string|null $template
     *
     * @return string
     */
    public function performanceFor($variations, $useDefault = true, string $template = null): ?string
    {
        if ($url = $this->getUrlFor($variations, $useDefault)) {
            return $this->performanceBuild($this->getUrlFor($variations, $useDefault), $template);
        }

        return null;
    }

    // -------------------------------------------------------
    // Системные
    // -------------------------------------------------------

    /**
     * Отобразить в качестве массива
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'          => $this->id->toString(),
            'extension'   => $this->getExtension(),
            'url'         => $this->getUrl(),
            'name'        => $this->getName(),
            'title'       => $this->getTitle(),
            'width'       => $this->getWidth(),
            'height'      => $this->getHeight(),
            'uploaded_at' => $this->uploadedAt()->timestamp,
            'variations'  => $this->variations->toArray(),
        ];
    }

    /**
     * Отобразить в качестве JSON
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);;
    }

    /**
     * Массив данных для хранения
     *
     * @return array
     */
    public function export(): array
    {
        $data['id'] = $this->getId()->toString();

        if (!empty($this->personalTitle)) {
            $data['title'] = $this->personalTitle;
        }

        return $data;
    }

    // -------------------------------------------------------
    // Хэлперы
    // -------------------------------------------------------

    protected function performanceBuild(string $url, string $template = null)
    {
        if (!isset($template)) {
            return '<img src="' . $url . '" alt="' . $this->getTitle() . '" title="' . $this->getTitle() . '" />';
        }

        if (mb_strpos($template, '{url}') !== false) {
            $template = str_replace('{url}', $url, $template);
        }

        if (mb_strpos($template, '{title}') !== false) {
            $template = str_replace('{title}', $this->getTitle(), $template);
        }

        return $template;
    }
}
