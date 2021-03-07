<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio\Container;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use ScottSmith\ErrorHandler\Integration\Mezzio\ErrorHandler;
use ScottSmith\ErrorHandler\Integration\Mezzio\ErrorResponseGenerator;

final class ErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $errorHandlerConfig = $config['error_handler'] ?? [];

        $isDebug = $errorHandlerConfig['debug'] ?? false;

        $template = $errorHandlerConfig['template_error'] ?? ErrorResponseGenerator::TEMPLATE_DEFAULT;
        $layout = array_key_exists('layout', $errorHandlerConfig)
            ? (string)$errorHandlerConfig['layout']
            : ErrorResponseGenerator::LAYOUT_DEFAULT;

        $renderer = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;

        return new ErrorResponseGenerator(
            $container->get(ErrorHandler::class),
            $isDebug,
            $template,
            $layout,
            $renderer
        );
    }
}
