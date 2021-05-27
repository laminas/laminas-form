<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

/**
 * FormSearch view helper
 *
 * The difference between the Text state and the Search state is primarily stylistic:
 * on platforms where search fields are distinguished from regular text fields,
 * the Search state might result in an appearance consistent with the platform's
 * search fields rather than appearing like a regular text field.
 */
class FormSearch extends FormText
{
    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'search';
    }
}
