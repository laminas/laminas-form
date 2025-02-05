<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\MultiCheckbox;

final class CustomElementWithConstructorDependency extends MultiCheckbox
{
    public function __construct(
        string|null $name = null,
        iterable $options = [],
        public readonly string $myString, // phpcs:ignore
    ) {
        parent::__construct($name, $options);
    }
}
