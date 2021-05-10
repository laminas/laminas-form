<?php

namespace Laminas\Form;

trait FormFactoryAwareTrait
{
    /**
     * @var Factory
     */
    protected $factory = null;

    /**
     * Compose a form factory into the object
     *
     * @param Factory $factory
     * @return mixed
     */
    public function setFormFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
