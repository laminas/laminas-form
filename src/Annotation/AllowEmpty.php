<?php

namespace Laminas\Form\Annotation;

use Attribute;
use Laminas\Filter\Boolean as BooleanFilter;

use function is_bool;

/**
 * AllowEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Laminas\InputFilter\Input should enable the allowEmpty flag.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @deprecated 2.4.8 Use `@Validator({"name":"NotEmpty"})` instead.
 */
#[Attribute]
class AllowEmpty
{
    /**
     * @var bool
     */
    protected $allowEmpty;

    /**
     * Receive and process the contents of an annotation
     *
     * @param bool|string $allowEmpty
     */
    public function __construct($allowEmpty = true)
    {
        if (! is_bool($allowEmpty)) {
            $filter   = new BooleanFilter();
            $allowEmpty = $filter->filter($allowEmpty);
        }

        $this->allowEmpty = $allowEmpty;
    }

    /**
     * Get value of required flag
     *
     * @return bool
     */
    public function getAllowEmpty(): bool
    {
        return $this->allowEmpty;
    }
}
