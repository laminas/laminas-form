<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;

class HydratorAwareModel implements HydratorAwareInterface
{
    /** @var null|HydratorInterface */
    protected $hydrator;
    /** @var null|string */
    protected $foo;
    /** @var null|string */
    protected $bar;

    public function setHydrator(HydratorInterface $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    public function getHydrator(): ?HydratorInterface
    {
        if (! $this->hydrator) {
            $this->hydrator = new ClassMethodsHydrator();
        }
        return $this->hydrator;
    }

    /**
     * @return $this
     */
    public function setFoo(string $value)
    {
        $this->foo = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function setBar(string $value)
    {
        $this->bar = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return string|null
     */
    public function getBar()
    {
        return $this->bar;
    }
}
