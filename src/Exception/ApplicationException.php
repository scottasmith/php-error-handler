<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Exception;

use Exception;
use Throwable;

class ApplicationException extends Exception
{
    /**
     * @var int
     */
    private int $applicationCode;

    /**
     * Meta to help track the error down
     *
     * @var array
     */
    private array $metadata = [];

    /**
     * @param string $template
     * @param int $applicationCode
     * @param Throwable|null $previousException
     * @param mixed ...$templateParams
     */
    public function __construct(
        string $template,
        int $applicationCode,
        Throwable $previousException = null,
        ...$templateParams
    ) {
        parent::__construct(
            sprintf($template, $templateParams),
            0,
            $previousException
        );

        $this->applicationCode = $applicationCode;
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
    public function getApplicationCode(): int
    {
        return $this->applicationCode;
    }
}
