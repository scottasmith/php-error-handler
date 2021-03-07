<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter\Interfaces;

use Throwable;

interface ReporterInterface
{
    /**
     * @param string $identifier
     * @param Throwable $throwable
     * @param array|null $metadata
     * @return mixed
     */
    public function report(string $identifier, Throwable $throwable, ?array $metadata = null);
}
