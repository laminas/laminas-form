<?php

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormRadio extends FormMultiCheckbox
{
    /**
     * Return input type
     *
     * @return string
     */
    protected function getInputType(): string
    {
        return 'radio';
    }

    /**
     * Get element name
     *
     * @return string
     */
    protected static function getName(ElementInterface $element): string
    {
        return (string) $element->getName();
    }
}
