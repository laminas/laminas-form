<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use IteratorAggregate as IteratorAggregateInterface;
use Traversable;

class IteratorAggregate implements IteratorAggregateInterface
{
    public function __construct(protected Traversable $iterator)
    {
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}
