<?php

declare(strict_types=1);

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
