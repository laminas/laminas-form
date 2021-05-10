<?php

namespace Laminas\Form;

interface ElementPrepareAwareInterface
{
    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form);
}
