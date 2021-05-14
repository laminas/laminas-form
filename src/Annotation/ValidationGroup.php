<?php

namespace Laminas\Form\Annotation;

/**
 * ValidationGroup annotation
 *
 * Allows passing validation group to the form
 *
 * The value should be an associative array.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @copyright  Copyright (c) 2005-2015 Laminas (https://www.zend.com)
 * @license    https://getlaminas.org/license/new-bsd     New BSD License
 */
class ValidationGroup
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $validationGroup
     */
    public function __construct(array $validationGroup = [])
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
