<?php

namespace Laminas\Form;

trait FormFactoryAwareTrait
{
    /** @var Factory */
    protected $factory;

    /**
     * Compose a form factory into the object
     *
     * @return mixed
     */
    public function setFormFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
