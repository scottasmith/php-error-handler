<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler;

class Identifier
{
    public static function getIdentifier(): string
    {
        // Get an RFC-4122 compliant globaly unique identifier
        $data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
