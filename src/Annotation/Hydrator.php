<?php

namespace Laminas\Form\Annotation;

/**
 * Hydrator annotation
 *
 * Use this annotation to specify a specific hydrator class to use with the form.
 * The value should be a string indicating the fully qualified class name of the
 * hydrator to use.
 *
 * @Annotation
 */
class Hydrator extends AbstractArrayOrStringAnnotation
{
    /**
     * Retrieve the hydrator class
     *
     * @return null|string|array
     */
    public function getHydrator()
    {
        return $this->value;
    }
}
