<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

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
