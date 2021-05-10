<?php

namespace LaminasTest\Form\TestAsset;

class ArrayModel extends Model
{
    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
