<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler;

class Identifier
{
    public static function getIdentifier(): string
    {
        return uniqid();
    }
}
