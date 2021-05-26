<?php

namespace LaminasTest\Form\TestAsset;

class HydratorStrategyEntityB
{
    /** @var int  */
    private $field1;
    /** @var string  */
    private $field2;

    public function __construct(int $field1, string $field2)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;
    }

    public function getField1(): int
    {
        return $this->field1;
    }

    public function getField2(): string
    {
        return $this->field2;
    }

    public function setField1(string $value): self
    {
        $this->field1 = $value;
        return $this;
    }

    public function setField2(string $value): self
    {
        $this->field2 = $value;
        return $this;
    }
}
