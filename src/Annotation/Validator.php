<?php

namespace Laminas\Form\Annotation;

use Attribute;

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
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::IS_REPEATABLE)]
class Validator
{
    /**
     * @var array
     */
    protected $validator;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $validator
     */
    public function __construct(array $validator = [])
    {
        $this->validator = $validator;
    }

    /**
     * Retrieve the validator specification
     *
     * @return array
     */
    public function getValidator(): array
    {
        return $this->validator;
    }
}
