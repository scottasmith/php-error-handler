<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio;

use ScottSmith\ErrorHandler\Exception\ApplicationException;
use Symfony\Component\Console\Event\ConsoleErrorEvent;

final class SymfonyConsoleErrorListener
{
    /**
     * @var ErrorHandler
     */
    private ErrorHandler $errorHandler;

    /**
     * @param ErrorHandler $errorHandler
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function __invoke(ConsoleErrorEvent $event)
    {
        $command = $event->getCommand();

        $exception = new ApplicationException(
            'Exception thrown in command %s',
            $event->getExitCode(),
            $event->getError(),
            $command->getName()
        );

        $identifier = $this->errorHandler->report($exception);

        $output = $event->getOutput();
        $output->writeln(sprintf(
            'Oops, exception thrown while running command <info>%s</info>. Please quote error <info>%s</info>',
            $command->getName(),
            $identifier
        ));
    }
}
