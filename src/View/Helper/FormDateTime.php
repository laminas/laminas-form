<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

/**
 * @deprecated 3.0.0 This element is deprecated starting with 3.0.0 as it has been removed from WHATWG HTML
 *
 * @see        https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime
 */
class FormDateTime extends FormInput
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

    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'datetime';
    }
}
