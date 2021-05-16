<?php

namespace Laminas\Form\Annotation;

use Attribute;
use Laminas\Form\Exception;

/**
 * InputFilter annotation
 *
 * Use this annotation to specify a specific input filter class to use with the
 * form. The value should be a string indicating the fully qualified class name
 * of the input filter to use.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
class InputFilter
{
    /**
     * @var string|array
     */
    protected $inputFilter;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string|array $inputFilter
     */
    public function __construct($inputFilter)
    {
        if (! is_array($inputFilter) && ! is_string($inputFilter)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define an array or string; received "%s"',
                get_class($this),
                isset($data['value']) ? gettype($data['value']) : 'null'
            ));
        }

        $this->inputFilter = $inputFilter;
    }
    /**
     * Retrieve the input filter class
     *
     * @return array|string
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}
