<?php

namespace LaminasTest\Form\TestAsset;

class Identifier
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
