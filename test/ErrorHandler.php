<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use ErrorException;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

/**
 * @internal
 */
final class ErrorHandler
{
    public static function setErrorHandler(): void
    {
        set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): void {
            if (! (error_reporting() & $errno)) {
                return;
            }

             throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    public static function restoreErrorHandler(): void
    {
        restore_error_handler();
    }
}
