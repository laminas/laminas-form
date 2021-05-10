<?php

namespace Laminas\Form\Annotation;

use Laminas\Form\Exception;

use function get_class;
use function gettype;
use function is_array;
use function sprintf;

abstract class AbstractArrayAnnotation
{
    /**
     * @var array
     */
    protected $value;

    /**
     * Receive and process the contents of an annotation
     *
     * @param  array $data
     * @throws Exception\DomainException if a 'value' key is missing, or its value is not an array
     */
    public function __construct(array $data)
    {
        if (! isset($data['value']) || ! is_array($data['value'])) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define an array; received "%s"',
                get_class($this),
                isset($data['value']) ? gettype($data['value']) : 'null'
            ));
        }
        $this->value = $data['value'];
    }
}
