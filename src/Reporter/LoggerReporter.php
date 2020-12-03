<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter;

use Psr\Log\LoggerInterface;
use Throwable;

class LoggerReporter extends AbstractReporter
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function vendorReport(string $identifier, Throwable $throwable, ?array $metadata = null)
    {
        $this->logger->error('Error: ' . $throwable->getMessage(), $metadata);
    }
}
