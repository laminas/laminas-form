<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Iterator;
use ReturnTypeWillChange;

use function current;
use function key;
use function next;
use function reset;

class CustomTraversable implements Iterator
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        reset($this->data);
    }
}
