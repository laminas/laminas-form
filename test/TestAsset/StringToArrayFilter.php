<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\AbstractFilter;

use function explode;
use function is_array;

class StringToArrayFilter extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (! is_array($value)) {
            return explode(',', $value);
        }
        return $value;
    }
}
