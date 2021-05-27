<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Stdlib\ArrayObject;

class CustomCollection extends ArrayObject
{
    public function toArray(): array
    {
        $ret = [];

        foreach ($this as $key => $obj) {
            $ret[$key] = $obj->toArray();
        }

        return $ret;
    }
}
