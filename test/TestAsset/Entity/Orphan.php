<?php

declare(strict_types=1);

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

    public function exchangeArray(array $data = []): void
    {
        $this->name = $data['name'];
    }
}
