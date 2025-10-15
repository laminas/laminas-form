<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\FilterInterface;

use function explode;
use function is_array;

final class StringToArrayFilter implements FilterInterface
{
    /**
     * @inheritDoc
     */
    public function filter(mixed $value): mixed
    {
        if (! is_array($value)) {
            return explode(',', (string) $value);
        }
        return $value;
    }

    public function __invoke(mixed $value): mixed
    {
        return $this->filter($value);
    }
}
