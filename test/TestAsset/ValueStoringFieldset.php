<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;

class ValueStoringFieldset extends Fieldset
{
    /** @var iterable */
    protected $storedValue;

    public function populateValues(iterable $data): void
    {
        $this->storedValue = $data;
        parent::populateValues($data);
    }

    public function getStoredValue(): iterable
    {
        return $this->storedValue;
    }
}
