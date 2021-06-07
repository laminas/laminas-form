<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

abstract class AbstractFormDateTime extends FormInput
{
    /**
     * Attributes valid for the input tag type="datetime"
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
        'readonly'     => true,
        'required'     => true,
        'step'         => true,
        'type'         => true,
        'value'        => true,
    ];
}
