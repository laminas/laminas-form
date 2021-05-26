<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Traversable;

class ValueStoringFieldset extends Fieldset
{
    /** @var array|Traversable */
    protected $storedValue;

    /**
     * @param array|Traversable $data
     */
    public function populateValues($data)
    {
        $this->storedValue = $data;
        parent::populateValues($data);
    }

    /**
     * @return array|Traversable
     */
    public function getStoredValue()
    {
        return $this->storedValue;
    }
}
