<?php

namespace Laminas\Form;

interface FormFactoryAwareInterface
{
    /**
     * Compose a form factory into the object
     */
    public function setFormFactory(Factory $factory);
}
