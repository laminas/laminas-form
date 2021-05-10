<?php

namespace Laminas\Form\Annotation;

/**
 * ErrorMessage annotation
 *
 * Allows providing an error message to seed the Input specification for a
 * given element. The content should be a string.
 *
 * @Annotation
 */
class ErrorMessage extends AbstractStringAnnotation
{
    /**
     * Retrieve the message
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->value;
    }
}
