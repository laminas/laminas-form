<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\Form\ElementPrepareAwareInterface;
use Laminas\Form\FormInterface;

class Password extends Element implements ElementPrepareAwareInterface
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'password',
    ];

    /**
     * Remove the password before rendering if the form fails in order to avoid any security issue
     */
    public function prepareElement(FormInterface $form): void
    {
        $this->setValue('');
    }
}
