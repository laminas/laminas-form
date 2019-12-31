<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

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
