<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Type annotation
 *
 * Use this annotation to specify the specific \Laminas\Form class to use when
 * building the form, fieldset, or element. The value should be a string
 * representing a fully qualified classname.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Type
{
    /** @var string */
    protected $type;

    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Retrieve the class type
     */
    public function getType(): string
    {
        return $this->type;
    }
}
