<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Laminas\Form\Exception;

use function gettype;
use function is_array;
use function is_string;
use function sprintf;

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
final class InputFilter
{
    /** @var string|array */
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
                static::class,
                gettype($inputFilter)
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
