<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Stdlib\ArrayObject;

class CustomCollection extends ArrayObject
{
    public function toArray()
    {
        $ret = [];

        foreach ($this as $key => $obj) {
            $ret[$key] = $obj->toArray();
        }

        return $ret;
    }
}
