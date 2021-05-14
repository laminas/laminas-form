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
 * @NamedArgumentConstructor
 * @deprecated 2.4.8 Use `@Validator({"name":"NotEmpty"})` instead.
 */
class ContinueIfEmpty
{
    /**
     * @var bool
     */
    protected $continueIfEmpty;

    /**
     * Receive and process the contents of an annotation
     *
     * @param bool|string $continueIfEmpty
     */
    public function __construct($continueIfEmpty = true)
    {
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
    public function getContinueIfEmpty(): bool
    {
        return $this->continueIfEmpty;
    }
}
