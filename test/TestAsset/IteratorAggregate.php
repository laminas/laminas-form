<?php

namespace LaminasTest\Form\TestAsset;

use IteratorAggregate as IteratorAggregateInterface;
use Traversable;

class IteratorAggregate implements IteratorAggregateInterface
{
    protected $iterator;

    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    public function getIterator()
    {
        return $this->iterator;
    }
}
