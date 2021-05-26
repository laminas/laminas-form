<?php

namespace LaminasTest\Form\TestAsset;

class Identifier
{
    /** @var int  */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
