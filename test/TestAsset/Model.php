<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use DomainException;
use Laminas\Stdlib\ArraySerializableInterface;

use function property_exists;

class Model implements ArraySerializableInterface
{
    protected $foo;
    protected $bar;
    protected $foobar;

    public function __set($name, $value)
    {
        throw new DomainException('Overloading to set values is not allowed');
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new DomainException('Unknown attribute');
    }

    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (! property_exists($this, $key)) {
                continue;
            }
            $this->$key = $value;
        }
    }

    public function getArrayCopy()
    {
        return [
            'foo'    => $this->foo,
            'bar'    => $this->bar,
            'foobar' => $this->foobar,
        ];
    }
}
