<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio;

use ScottSmith\ErrorHandler\Integration\Mezzio\Container\ErrorHandlerFactory;
use ScottSmith\ErrorHandler\Integration\Mezzio\Container\LoggerReporterFactory;
use ScottSmith\ErrorHandler\Reporter\LaravelBugsnagReporter;
use ScottSmith\ErrorHandler\Reporter\LoggerReporter;
use ScottSmith\ErrorHandler\Reporter\NullReporter;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'factories' => [
                ErrorHandler::class => ErrorHandlerFactory::class,
                LoggerReporter::class => LoggerReporterFactory::class
            ],

            'invokables' => [
                NullReporter::class,
                LaravelBugsnagReporter::class,
            ],
        ];
    }
}
