<?php

namespace LaminasTest\Form\TestAsset;

use Iterator;

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
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        return reset($this->data);
    }
}
