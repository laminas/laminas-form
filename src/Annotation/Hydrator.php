<?php

namespace Laminas\Form\Annotation;

use Laminas\Form\Exception;

/**
 * Hydrator annotation
 *
 * Use this annotation to specify a specific hydrator class to use with the form.
 * The value should be a string indicating the fully qualified class name of the
 * hydrator to use.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
class Hydrator
{
    /**
     * @var string|array
     */
    protected $hydrator;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string|array $hydrator
     */
    public function __construct($hydrator)
    {
        if (! is_array($hydrator) && ! is_string($hydrator)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define an array or string; received "%s"',
                get_class($this),
                isset($data['value']) ? gettype($data['value']) : 'null'
            ));
        }

        $this->hydrator = $hydrator;
    }

    /**
     * Retrieve the hydrator class
     *
     * @return string|array
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
