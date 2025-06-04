<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;

/** @final */
class Text extends Element
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'text',
    ];
}
