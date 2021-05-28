<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Laminas\Filter\Boolean as BooleanFilter;

use function is_bool;

/**
 * AllowEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Laminas\InputFilter\Input should enable the allowEmpty flag.
 *
 * @deprecated 2.4.8 Use `@Validator({"name":"NotEmpty"})` instead.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class AllowEmpty
{
    /** @var bool */
    protected $allowEmpty;

    /**
     * Receive and process the contents of an annotation
     *
     * @param bool|string $allowEmpty
     */
    public function __construct($allowEmpty = true)
    {
        if (! is_bool($allowEmpty)) {
            $filter     = new BooleanFilter();
            $allowEmpty = $filter->filter($allowEmpty);
        }

        $this->allowEmpty = $allowEmpty;
    }

    /**
     * Get value of required flag
     */
    public function getAllowEmpty(): bool
    {
        return $this->allowEmpty;
    }
}
