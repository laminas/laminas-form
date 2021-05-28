<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Attributes annotation
 *
 * Expects an array of attributes. The value is used to set any attributes on
 * the related form object (element, fieldset, or form).
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Attributes
{
    /** @var array */
    protected $attributes;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Retrieve the attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
