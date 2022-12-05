<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Stdlib\ArrayObject;

/**
 * @template TKey of array-key
 * @template TValue
 * @extends  ArrayObject<TKey, TValue>
 */
class CustomCollection extends ArrayObject
{
    /** @return array<TKey, TValue> */
    public function toArray(): array
    {
        $ret = [];

        foreach ($this as $key => $obj) {
            $ret[$key] = $obj->toArray();
        }

        return $ret;
    }
}
