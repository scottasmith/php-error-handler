<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter\Interfaces;

interface MetaGeneratorAwareInterface
{
    /**
     * @param MetaGeneratorInterface $generator
     */
    public function registerMetaGenerator(MetaGeneratorInterface $generator): void;
}
