<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio\Container;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ScottSmith\ErrorHandler\Reporter\LoggerReporter;

final class LoggerReporterFactory
{
    public function __invoke(ContainerInterface $container) : LoggerReporter
    {
        return new LoggerReporter($container->get(LoggerInterface::class));
    }
}
