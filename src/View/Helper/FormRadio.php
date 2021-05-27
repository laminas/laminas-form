<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormRadio extends FormMultiCheckbox
{
    /**
     * Return input type
     */
    protected function getInputType(): string
    {
        return 'radio';
    }

    /**
     * Get element name
     */
    protected static function getName(ElementInterface $element): string
    {
        return (string) $element->getName();
    }
}
