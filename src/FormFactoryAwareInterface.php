<?php

declare(strict_types=1);

namespace Laminas\Form;

interface FormFactoryAwareInterface
{
    /**
     * Compose a form factory into the object
     */
    public function setFormFactory(Factory $formFactory);
}
