<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

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
