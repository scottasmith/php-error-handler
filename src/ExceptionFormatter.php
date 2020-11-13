<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler;

use Illuminate\Validation\ValidationException;
use ScottSmith\ErrorHandler\Exception\ApplicationException;
use Throwable;

class ExceptionFormatter
{
    /**
     * Return the application exception message if instance of AbstractApplicationException and
     * the isDebug flag is set
     *
     * @param Throwable $throwable
     * @param bool $isDebug
     * @return string
     */
    public static function getMessage(Throwable $throwable, bool $isDebug = false): string
    {
         return ($isDebug && $throwable instanceof ApplicationException)
            ? $throwable->getMessage()
            : 'An unexpected error has occurred';
    }

    /**
     * Return the application code is instance of AbstractApplicationException or just the Exception code
     *
     * @param Throwable $throwable
     * @return int
     */
    public static function getCode(Throwable $throwable): int
    {
        return $throwable instanceof ApplicationException
            ? $throwable->getApplicationCode()
            : $throwable->getCode();
    }

    /**
     * Return the stack trace of the exception if available
     *
     * @param Throwable $throwable
     * @param bool $isDebug
     * @return array|null
     */
    public static function getStackTrace(Throwable $throwable, bool $isDebug = false): ?array
    {
        return $isDebug ? explode("\n", $throwable->getTraceAsString()) : null;
    }

    /**
     * Return the errors from the validation exception fields if applicable
     *
     * @param Throwable $throwable
     * @return array|null
     */
    public static function getValidationException(Throwable $throwable): ?array
    {
        return $throwable instanceof ValidationException
            ? $throwable->errors()
            : null;
    }
}
