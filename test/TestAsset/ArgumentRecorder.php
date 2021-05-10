<?php

namespace LaminasTest\Form\TestAsset;

class ArgumentRecorder
{
    public $args;

    public function __construct(...$args)
    {
        $this->args = $args;
    }
}
