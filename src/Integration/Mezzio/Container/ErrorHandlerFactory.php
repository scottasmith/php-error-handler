<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio\Container;

use Psr\Container\ContainerInterface;
use RuntimeException;
use ScottSmith\ErrorHandler\Integration\Mezzio\ErrorHandler;
use ScottSmith\ErrorHandler\Reporter\Interfaces\ReporterInterface;
use ScottSmith\ErrorHandler\Reporter\NullReporter;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $errorHandlerConfig = $config['error_handler'] ?? [];

        $reporter = $this->getReporter($container, $errorHandlerConfig);

        return new ErrorHandler($reporter);
    }

    /**
     * @param ContainerInterface $container
     * @param array $config
     * @return ReporterInterface
     */
    private function getReporter(ContainerInterface $container, array $config): ReporterInterface
    {
        $reporter = $config['reporter'] ?? NullReporter::class;

        if (!class_exists($reporter)) {
            throw new RuntimeException('reporter must be a valid class: ' . ReporterInterface::class);
        }

        $reporterClass = $container->get($reporter);
        if (!$reporterClass instanceof ReporterInterface) {
            throw new RuntimeException('reporter must be instance of ' . ReporterInterface::class);
        }

        return $reporterClass;
    }
}
