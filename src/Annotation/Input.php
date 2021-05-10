<?php

namespace Laminas\Form\Annotation;

/**
 * Input annotation
 *
 * Use this annotation to specify a specific input class to use with an element.
 * The value should be a string indicating the fully qualified class name of the
 * input to use.
 *
 * @Annotation
 */
class Input extends AbstractStringAnnotation
{
    /**
     * Retrieve the input class
     *
     * @return null|string
     */
    public function getInput()
    {
        return $this->value;
    }
}
