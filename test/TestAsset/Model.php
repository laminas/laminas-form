<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use DomainException;
use Laminas\Stdlib\ArraySerializableInterface;

use function property_exists;

class Model implements ArraySerializableInterface
{
    /** @var mixed */
    protected $foo;
    /** @var mixed */
    protected $bar;
    /** @var mixed */
    protected $foobar;

    public function __set(string $name, mixed $value)
    {
        throw new DomainException('Overloading to set values is not allowed');
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new DomainException('Unknown attribute');
    }

    /**
     * @inheritDoc
     */
    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (! property_exists($this, $key)) {
                continue;
            }
            $this->$key = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy()
    {
        return [
            'foo'    => $this->foo,
            'bar'    => $this->bar,
            'foobar' => $this->foobar,
        ];
    }
}
