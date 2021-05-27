<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;

class Textarea extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'textarea',
    ];
}
