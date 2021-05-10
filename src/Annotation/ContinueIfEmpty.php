<?php

namespace Laminas\Form\Annotation;

use Laminas\Filter\Boolean as BooleanFilter;

use function is_bool;

/**
 * ContinueIfEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Laminas\InputFilter\Input should enable the continueIfEmpty flag.
 *
 * @Annotation
 * @deprecated 2.4.8 Use `@Validator({"name":"NotEmpty"})` instead.
 */
class ContinueIfEmpty
{
    /**
     * @var bool
     */
    protected $continueIfEmpty = true;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $continueIfEmpty = isset($data['value'])
            ? $data['value']
            : false;

        if (! is_bool($continueIfEmpty)) {
            $filter = new BooleanFilter();
            $continueIfEmpty = $filter->filter($continueIfEmpty);
        }

        $this->continueIfEmpty = $continueIfEmpty;
    }

    /**
     * Get value of required flag
     *
     * @return bool
     */
    public function getContinueIfEmpty()
    {
        return $this->continueIfEmpty;
    }
}
