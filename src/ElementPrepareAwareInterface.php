<?php

namespace Laminas\Form;

interface ElementPrepareAwareInterface
{
    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form);
}
