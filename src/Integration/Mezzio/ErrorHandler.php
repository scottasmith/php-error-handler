<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio;

use ScottSmith\ErrorHandler\Identifier;
use ScottSmith\ErrorHandler\Reporter\Interfaces\ReporterInterface;
use Throwable;

final class ErrorHandler
{
    /**
     * @var ReporterInterface
     */
    private ReporterInterface $reporter;

    /**
     * ErrorHandler constructor.
     * @param ReporterInterface $reporter
     */
    public function __construct(ReporterInterface $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @param Throwable $throwable
     * @return string - identifier
     */
    public function report(Throwable $throwable): string
    {
        $identifier = Identifier::getIdentifier();

        $this->reporter->report($identifier, $throwable);

        return $identifier;
    }
}
