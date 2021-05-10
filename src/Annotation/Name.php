<?php

namespace Laminas\Form\Annotation;

/**
 * Name annotation
 *
 * Use this annotation to specify a name other than the property or class name
 * when building the form, element, or input. The value should be a string.
 *
 * @Annotation
 */
class Name extends AbstractStringAnnotation
{
    /**
     * Retrieve the name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->value;
    }
}
