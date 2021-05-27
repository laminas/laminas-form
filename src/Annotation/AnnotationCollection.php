<?php

declare(strict_types=1);

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
     */
    public function hasAnnotation(string $class): bool
    {
        foreach ($this as $annotation) {
            if (get_class($annotation) === $class) {
                return true;
            }
        }

        return false;
    }
}
