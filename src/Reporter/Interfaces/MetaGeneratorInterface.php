<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter\Interfaces;

interface MetaGeneratorInterface
{
    public function generateMetaData(): array;
}
