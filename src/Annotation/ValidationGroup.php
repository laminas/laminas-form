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
final class ValidationGroup
{
    /** @var array */
    protected $validationGroup;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $validationGroup
     */
    public function __construct(array $validationGroup)
    {
        $this->validationGroup = $validationGroup;
    }

    /**
     * Retrieve the options
     *
     * @return array
     */
    public function getValidationGroup(): array
    {
        return $this->validationGroup;
    }
}
