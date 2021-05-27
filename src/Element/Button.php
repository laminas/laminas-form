<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;

class Button extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'button',
    ];
}
