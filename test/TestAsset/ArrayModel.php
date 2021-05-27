<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

class ArrayModel extends Model
{
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
