<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter;

use ScottSmith\ErrorHandler\Exception\ApplicationException;
use ScottSmith\ErrorHandler\Reporter\Interfaces\{
    MetaGeneratorAwareInterface,
    MetaGeneratorInterface,
    ReporterInterface
};
use Throwable;

abstract class AbstractReporter implements ReporterInterface, MetaGeneratorAwareInterface
{
    /**
     * Generators to allow extra information to be injected
     *
     * @var MetaGeneratorInterface[]
     */
    private array $generators = [];

    /**
     * @inheritDoc
     */
    public function registerMetaGenerator(MetaGeneratorInterface $generator): void
    {
        $generators[] = $generator;
    }

    /**
     * @inheritDoc
     */
    public function report(string $identifier, Throwable $throwable, ?array $metadata = null)
    {
        $vendorData = [
            'error' => [
                'identifier' => $identifier,
                'message' => $throwable->getMessage(),
                'code' => ($throwable instanceof ApplicationException)
                    ? $throwable->getApplicationCode()
                    : $throwable->getCode(),
            ],
        ];

        if (!empty($metadata)) {
            $vendorData['metadata'] = $metadata;
        }

        if ($throwable instanceof ApplicationException && $throwable->hasMetaData()) {
            $vendorData['metadata'] = array_merge($vendorData['metadata'], $throwable->getMetaData());
        }

        if (($previousException = $throwable->getPrevious()) !== null) {
            $vendorData['previous'] = [
                'message' => $previousException->getMessage(),
                'code' => $previousException->getCode(),
                'line' => $previousException->getLine(),
                'file' => $previousException->getFile(),
                'stacktrace' => explode("\n", $previousException->getTraceAsString()),
            ];
        }

        foreach ($this->generators as $generator) {
            $vendorData = array_merge($vendorData, $generator->generateMetaData());
        }

        $this->vendorReport($identifier, $throwable, $vendorData);
    }

    /**
     * @param string $identifier
     * @param Throwable $throwable
     * @param array|null $metadata
     * @return mixed
     */
    abstract protected function vendorReport(
        string $identifier,
        Throwable $throwable,
        ?array $metadata = null
    );
}
