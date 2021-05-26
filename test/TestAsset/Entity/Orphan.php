<?php

namespace LaminasTest\Form\TestAsset\Entity;

use function get_object_vars;

class Orphan
{
    /** @var string */
    public $name;

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function exchangeArray(array $data = [])
    {
        $this->name = $data['name'];
    }
}
