<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;

/**
 * This test asset targest laminas-hydrator v3, and will be aliased to
 * HydratorAwareModel when that version is installed.
 */
class HydratorAwareModelHydratorV3 implements HydratorAwareInterface
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
