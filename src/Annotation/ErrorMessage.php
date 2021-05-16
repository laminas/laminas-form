<?php

namespace Laminas\Form\Annotation;

use Attribute;

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
class ErrorMessage
{
    /**
     * @var string
     */
    protected $message;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string|null $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Retrieve the message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
