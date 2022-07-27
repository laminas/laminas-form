<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

class Identifier
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
