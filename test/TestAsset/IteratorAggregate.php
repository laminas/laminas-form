<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use IteratorAggregate as IteratorAggregateInterface;
use Traversable;

class IteratorAggregate implements IteratorAggregateInterface
{
    /** @var Traversable  */
    protected $iterator;

    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}
