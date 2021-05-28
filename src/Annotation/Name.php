<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Name annotation
 *
 * Use this annotation to specify a name other than the property or class name
 * when building the form, element, or input. The value should be a string.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Name
{
    /** @var string */
    protected $name;

    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the name
     */
    public function getName(): string
    {
        return $this->name;
    }
}
