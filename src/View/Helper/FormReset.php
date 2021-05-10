<?php

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

class FormReset extends FormInput
{
    /**
     * Attributes valid for the input tag type="reset"
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'           => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'type'           => true,
        'value'          => true,
    ];

    /**
     * Translatable attributes
     *
     * @var array
     */
    protected $translatableAttributes = [
        'value' => true,
    ];

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'reset';
    }
}
