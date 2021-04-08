<?php

namespace MasterDmx\LaravelMedia\Images;

use Intervention\Image\Image;
use MasterDmx\LaravelMedia\Contexts\Context;
use MasterDmx\LaravelMedia\Exceptions\ValidationException;

class ImageValidator
{
    /**
     * Минимальная ширина
     */
    public const RULE_MIN_WIDTH = 'min_width';

    /**
     * Максимальная ширина
     */
    public const RULE_MAX_WIDTH = 'max_width';

    /**
     * Минимальная высота
     */
    public const RULE_MIN_HEIGHT = 'min_height';

    /**
     * Максимальная высота
     */
    public const RULE_MAX_HEIGHT = 'max_height';

    private Context $context;
    private Image $image;

    /**
     * Массив ошибок
     *
     * @var array
     */
    private array $errors;

    /**
     * ImageValidator constructor.
     *
     * @param Context $context
     * @param Image   $image
     */
    public function __construct(Context $context, Image $image)
    {
        $this->context = $context;
        $this->image = $image;
    }

    /**
     * Запуск валидации
     */
    public function validate(): void
    {
        $rules = $this->context->rules();

        if (!empty($rules)) {
            if (isset($rules[static::RULE_MAX_HEIGHT]) && !$this->checkMaxHeight($rules[static::RULE_MAX_HEIGHT])) {
                $message = $this->parseMessage(
                    config('media.validation_messages.max_height', ''),
                    static::RULE_MAX_HEIGHT,
                    $rules[static::RULE_MAX_HEIGHT]
                );

                $this->addError(static::RULE_MAX_HEIGHT, $message);
            }

            // Минимальная высота
            if (isset($rules[static::RULE_MIN_HEIGHT]) && !$this->checkMinHeight($rules[static::RULE_MIN_HEIGHT])) {
                $message = $this->parseMessage(
                    config('media.validation_messages.min_height', ''),
                    static::RULE_MIN_HEIGHT,
                    $rules[static::RULE_MIN_HEIGHT]
                );

                $this->addError(static::RULE_MIN_HEIGHT, $message);
            }

            // Минимальная ширина
            if (isset($rules[static::RULE_MIN_WIDTH]) && !$this->checkMinWidth($rules[static::RULE_MIN_WIDTH])) {
                $message = $this->parseMessage(
                    config('media.validation_messages.min_width', ''),
                    static::RULE_MIN_WIDTH,
                    $rules[static::RULE_MIN_WIDTH]
                );

                $this->addError(static::RULE_MIN_WIDTH, $message);
            }

            // Максимальная ширина
            if (isset($rules[static::RULE_MAX_WIDTH]) && !$this->checkMaxWidth($rules[static::RULE_MAX_WIDTH])) {
                $message = $this->parseMessage(
                    config('media.validation_messages.max_width', ''),
                    static::RULE_MAX_WIDTH,
                    $rules[static::RULE_MAX_WIDTH]
                );

                $this->addError(static::RULE_MAX_WIDTH, $message);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }
    }

    public static function start(Context $context, Image $image): self
    {
        $validator = new static($context, $image);
        $validator->validate();

        return $validator;
    }

    protected function parseMessage(string $message, $ruleKey, $ruleValue)
    {
        if (mb_strpos($message, '{width}') !== false) {
            $message = str_replace('{width}', $this->image->width(), $message);
        }

        if (mb_strpos($message, '{height}') !== false) {
            $message = str_replace('{height}', $this->image->height(), $message);
        }

        if (mb_strpos($message, '{rule_' . $ruleKey . '}') !== false) {
            $message = str_replace('{rule_' . $ruleKey . '}', $ruleValue, $message);
        }

        return $message;
    }

    protected function addError(string $rule, string $message = '')
    {
        $this->errors[] = [
            'rule' => $rule,
            'message' => $message,
        ];
    }

    // ------------------------------------
    // Обработка правил
    // -----------------------------------

    protected function checkMinWidth(int $width): bool
    {
        return $this->image->width() >= $width;
    }

    protected function checkMaxWidth(int $width): bool
    {
        return $this->image->width() <= $width;
    }

    protected function checkMaxHeight(int $height): bool
    {
        return $this->image->height() <= $height;
    }

    protected function checkMinHeight(int $height): bool
    {
        return $this->image->height() >= $height;
    }
}
