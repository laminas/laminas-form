<?php

namespace Laminas\Form\Annotation;

use Attribute;

/**
 * Name annotation
 *
 * Use this annotation to specify a name other than the property or class name
 * when building the form, element, or input. The value should be a string.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
class Name
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
