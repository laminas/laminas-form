<?php

namespace Laminas\Form\Annotation;

/**
 * Options annotation
 *
 * Allows passing element, fieldset, or form options to the form factory.
 * Options are used to alter the behavior of the object they address.
 *
 * The value should be an associative array.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
class Options
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Retrieve the options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
