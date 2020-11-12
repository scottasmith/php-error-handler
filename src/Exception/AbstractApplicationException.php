<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Exception;

use Exception;
use Throwable;

abstract class AbstractApplicationException extends Exception
{
    /**
     * Meta to help track the error down
     *
     * @var array
     */
    private array $metadata = [];

    /**
     * @param string $template
     * @param int $code
     * @param Throwable $previousException
     * @param mixed ...$templateParams
     * @return static
     */
    public static function createGenericException(
        string $template,
        int $code,
        Throwable $previousException,
        ...$templateParams
    ): self {
        return new static(
            sprintf($template, $templateParams),
            $code,
            $previousException
        );
    }

    /**
     * Return cloned $this with extra meta
     *
     * @param array $metadata
     * @return $this
     */
    public function withMetaData(array $metadata): self
    {
        $this->metadata = array_merge(
            $this->metadata,
            $metadata
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function hasMetaData(): bool
    {
        return !empty($this->metadata);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->metadata;
    }

    /**
     * Application code (to be overridden)
     *
     * @return int
     */
    abstract public function getApplicationCode(): int;
}
