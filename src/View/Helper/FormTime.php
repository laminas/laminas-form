<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormTime extends AbstractFormDateTime
{
    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'time';
    }
}
