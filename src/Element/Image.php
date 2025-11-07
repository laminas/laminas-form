<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;

class Image extends Element
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'image',
    ];
}
