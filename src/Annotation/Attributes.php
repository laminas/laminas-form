<?php

namespace Laminas\Form\Annotation;

/**
 * Attributes annotation
 *
 * Expects an array of attributes. The value is used to set any attributes on
 * the related form object (element, fieldset, or form).
 *
 * @Annotation
 */
class Attributes extends AbstractArrayAnnotation
{
    /**
     * Retrieve the attributes
     *
     * @return null|array
     */
    public function getAttributes()
    {
        return $this->value;
    }
}
