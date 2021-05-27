<?php

declare(strict_types=1);

namespace Laminas\Form;

interface ElementPrepareAwareInterface
{
    /**
     * Prepare the form element (mostly used for rendering purposes)
     */
    public function prepareElement(FormInterface $form): void;
}
