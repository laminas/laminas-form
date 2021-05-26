<?php

namespace LaminasTest\Form\TestAsset;

class ArgumentRecorder
{
    /** @var array */
    public $args;

    public function __construct(string $name, array $options)
    {
        $this->args = [
            $name,
            $options,
        ];
    }
}
