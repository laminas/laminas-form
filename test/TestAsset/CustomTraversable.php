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

    public function current()
    {
        return current($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return $this->key() !== null;
    }

    public function rewind()
    {
        return reset($this->data);
    }
}
