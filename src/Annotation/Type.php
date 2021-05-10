<?php

namespace Laminas\Form\Annotation;

/**
 * Type annotation
 *
 * Use this annotation to specify the specific \Laminas\Form class to use when
 * building the form, fieldset, or element. The value should be a string
 * representing a fully qualified classname.
 *
 * @Annotation
 */
class Type extends AbstractStringAnnotation
{
    /**
     * Retrieve the class type
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->value;
    }
}
