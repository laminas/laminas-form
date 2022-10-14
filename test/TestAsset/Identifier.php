<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Stringable;

class Identifier implements Stringable
{
    public function __construct(private int $id)
    {
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
