<?php

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\Form\ElementPrepareAwareInterface;
use Laminas\Form\FormInterface;

class Password extends Element implements ElementPrepareAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'password',
    ];

    /**
     * Remove the password before rendering if the form fails in order to avoid any security issue
     *
     * @return void
     */
    public function prepareElement(FormInterface $form)
    {
        $this->setValue('');
    }
}
