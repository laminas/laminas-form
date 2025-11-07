<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\MultiCheckbox;

final class CustomElementWithRequiredOption extends MultiCheckbox
{
    public readonly string $myString;

    /** @param array<array-key, mixed> $options */
    public function __construct(string|null $name = null, iterable $options = [])
    {
        /**
         * The null assignment causes a type error which is desired
         *
         * @psalm-suppress PossiblyNullPropertyAssignmentValue
         */
        $this->myString = $options['requiredOption'] ?? null;

        parent::__construct($name, $options);
    }
}
