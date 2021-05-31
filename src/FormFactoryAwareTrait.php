<?php

declare(strict_types=1);

namespace Laminas\Form;

trait FormFactoryAwareTrait
{
    /** @var null|Factory */
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
