<?php

namespace Laminas\Form\Annotation;

use ArrayObject;

use function get_class;

/**
 * @extends ArrayObject<array-key, object>
 */
final class AnnotationCollection extends ArrayObject
{
    /**
     * Checks if the collection has annotations for a class
     *
     * @param  string $class
     * @return bool
     */
    public function hasAnnotation($class)
    {
        foreach ($this as $annotation) {
            if (get_class($annotation) == $class) {
                return true;
            }
        }

        return false;
    }
}
