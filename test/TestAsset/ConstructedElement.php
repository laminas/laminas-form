<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;

class ConstructedElement extends Element
{
    public $constructedKey;

    /**
     * @param null|int|string $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {
        if (isset($options['constructedKey'])) {
            $this->constructedKey = $options['constructedKey'];
        }
        parent::__construct($name, $options);
    }
}
