<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * ErrorMessage annotation
 *
 * Allows providing an error message to seed the Input specification for a
 * given element. The content should be a string.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class ErrorMessage
{
    /** @var string */
    protected $message;

    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Retrieve the message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
