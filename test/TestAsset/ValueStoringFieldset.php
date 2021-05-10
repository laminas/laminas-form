<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;

class ValueStoringFieldset extends Fieldset
{
    protected $storedValue;

    public function populateValues($data)
    {
        $this->storedValue = $data;
        parent::populateValues($data);
    }

    public function getStoredValue()
    {
        return $this->storedValue;
    }
}
