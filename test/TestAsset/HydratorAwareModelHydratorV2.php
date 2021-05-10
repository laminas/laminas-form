<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;

/**
 * This test asset targest laminas-hydrator v1 and v2, and will be aliased to
 * HydratorAwareModel when of those versions is installed.
 */
class HydratorAwareModelHydratorV2 implements HydratorAwareInterface
{
    protected $hydrator = null;

    protected $foo = null;
    protected $bar = null;

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function getHydrator()
    {
        if (! $this->hydrator) {
            $this->hydrator = new ClassMethods();
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
