<?php

declare(strict_types=1);

namespace Laminas\Form\Exception;

use RuntimeException;

class IncompatiblePhpVersionException extends RuntimeException implements
    ExceptionInterface
{
}
