<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;

class HydratorAwareModel implements HydratorAwareInterface
{
    protected $hydrator = null;

    protected $foo = null;
    protected $bar = null;

    public function setHydrator(HydratorInterface $hydrator) : void
    {
        $this->hydrator = $hydrator;
    }

    public function getHydrator() : ?HydratorInterface
    {
        if (! $this->hydrator) {
            $this->hydrator = new ClassMethodsHydrator();
        }
        return $this->hydrator;
    }

    public function setFoo($value)
    {
        $this->foo = $value;
        return $this;
    }

    public function setBar($value)
    {
        $this->bar = $value;
        return $this;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
