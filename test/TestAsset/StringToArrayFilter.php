<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\AbstractFilter;

use function explode;
use function is_array;

/**
 * @extends AbstractFilter<array{}>
 */
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
