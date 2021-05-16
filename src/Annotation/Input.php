<?php

namespace Laminas\Form\Annotation;

use Attribute;

/**
 * Input annotation
 *
 * Use this annotation to specify a specific input class to use with an element.
 * The value should be a string indicating the fully qualified class name of the
 * input to use.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
class Input
{
    /**
     * @var string
     */
    protected $input;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    /**
     * Retrieve the input class
     *
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }
}
