<?php

namespace Laminas\Form\Annotation;

/**
 * Validator annotation
 *
 * Expects an associative array defining the validator.
 *
 * Typically, this includes the "name" with an associated string value
 * indicating the validator name or class, and optionally an "options" key
 * with an object/associative array value of options to pass to the
 * validator constructor.
 *
 * This annotation may be specified multiple times; validators will be added
 * to the validator chain in the order specified.
 *
 * @Annotation
 */
class Validator extends AbstractArrayAnnotation
{
    /**
     * Retrieve the validator specification
     *
     * @return null|array
     */
    public function getValidator()
    {
        return $this->value;
    }
}
