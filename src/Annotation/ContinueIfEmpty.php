<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Laminas\Filter\Boolean as BooleanFilter;

use function is_bool;

/**
 * ContinueIfEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Laminas\InputFilter\Input should enable the continueIfEmpty flag.
 *
 * @deprecated 2.4.8 Use `@Validator({"name":"NotEmpty"})` instead.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class ContinueIfEmpty
{
    private bool $continueIfEmpty;

    /**
     * Receive and process the contents of an annotation
     *
     * @param bool|string $continueIfEmpty
     */
    public function __construct($continueIfEmpty = true)
    {
        if (! is_bool($continueIfEmpty)) {
            $filter          = new BooleanFilter();
            $continueIfEmpty = $filter->filter($continueIfEmpty);
        }

        $this->continueIfEmpty = $continueIfEmpty;
    }

    /**
     * Get value of required flag
     */
    public function getContinueIfEmpty(): bool
    {
        return $this->continueIfEmpty;
    }
}
