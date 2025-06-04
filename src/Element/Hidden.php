<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;

/** @final */
class Hidden extends Element
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'hidden',
    ];
}
