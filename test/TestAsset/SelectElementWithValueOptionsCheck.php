<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\Select;

use function assert;

final class SelectElementWithValueOptionsCheck extends Select
{
    public function init(): void
    {
        assert($this->valueOptions !== []);
    }
}
