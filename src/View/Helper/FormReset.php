<?php

declare(strict_types=1);

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
        'name'      => true,
        'autofocus' => true,
        'disabled'  => true,
        'form'      => true,
        'type'      => true,
        'value'     => true,
    ];

    /**
     * Translatable attributes
     *
     * @var array<string, bool>
     */
    protected $translatableAttributes = [
        'value' => true,
    ];

    /**
     * Determine input type to use
     *
     * @throws Exception\DomainException
     */
    protected function getType(ElementInterface $element): string
    {
        return 'reset';
    }
}
