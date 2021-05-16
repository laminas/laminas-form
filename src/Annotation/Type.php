<?php

namespace Laminas\Form\Annotation;

use Attribute;

/**
 * Type annotation
 *
 * Use this annotation to specify the specific \Laminas\Form class to use when
 * building the form, fieldset, or element. The value should be a string
 * representing a fully qualified classname.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
class Type
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Retrieve the class type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
