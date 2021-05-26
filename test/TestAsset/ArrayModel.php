<?php

namespace LaminasTest\Form\TestAsset;

class ArrayModel extends Model
{
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
