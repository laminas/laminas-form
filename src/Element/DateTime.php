<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

/**
 * @deprecated 3.0.0 This element is deprecated starting with 3.0.0 as it has been removed from WHATWG HTML
 *
 * @see        https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime
 */
class DateTime extends AbstractDateTime
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'datetime',
    ];
}
