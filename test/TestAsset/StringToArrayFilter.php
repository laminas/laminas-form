<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\AbstractFilter;

class StringToArrayFilter extends AbstractFilter
{
    public function filter($value)
    {
        if (! is_array($value)) {
            return explode(',', $value);
        }
        return $value;
    }
}
