<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * ValidationGroup annotation
 *
 * Allows passing validation group to the form
 *
 * The value should be an associative array.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final readonly class ValidationGroup
{
    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(private array $validationGroup)
    {
    }

    /**
     * Retrieve the options
     */
    public function getValidationGroup(): array
    {
        return $this->validationGroup;
    }
}
