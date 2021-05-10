<?php

namespace LaminasTest\Form\TestAsset\Entity;

use function get_object_vars;

class Orphan
{
    /**
     * Name
     * @var string
     */
    public $name;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data = [])
    {
        $this->name = $data['name'];
    }
}
