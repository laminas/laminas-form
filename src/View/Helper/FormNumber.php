<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormNumber extends FormInput
{
    /**
     * Attributes valid for the input tag type="number"
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'         => true,
        'autocomplete' => true,
        'autofocus'    => true,
        'disabled'     => true,
        'form'         => true,
        'list'         => true,
        'max'          => true,
        'min'          => true,
        'step'         => true,
        'placeholder'  => true,
        'readonly'     => true,
        'required'     => true,
        'type'         => true,
        'value'        => true,
    ];

    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'number';
    }
}
