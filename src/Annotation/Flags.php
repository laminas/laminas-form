<?php

namespace Laminas\Form\Annotation;

/**
 * Flags annotation
 *
 * Allows passing flags to the form factory. These flags are used to indicate
 * metadata, and typically the priority (order) in which an element will be
 * included.
 *
 * The value should be an associative array.
 *
 * @Annotation
 */
class Flags extends AbstractArrayAnnotation
{
    /**
     * Retrieve the flags
     *
     * @return null|array
     */
    public function getFlags()
    {
        return $this->value;
    }
}
