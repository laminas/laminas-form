<?php

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormTime extends FormDateTime
{
    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'time';
    }
}
