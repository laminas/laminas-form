<?php

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormDateTimeLocal extends FormDateTime
{
    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'datetime-local';
    }
}
