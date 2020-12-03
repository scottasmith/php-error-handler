<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter;

use ScottSmith\ErrorHandler\Reporter\Interfaces\ReporterInterface;
use Throwable;

class NullReporter implements ReporterInterface
{
    public function report(string $identifier, Throwable $throwable, ?array $metadata = null)
    {
        // This is the default reported that does nothing
    }
}
