<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\Form\ElementPrepareAwareInterface;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputProviderInterface;

class File extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'file',
    ];

    /**
     * Prepare the form element (mostly used for rendering purposes)
     */
    public function prepareElement(FormInterface $form): void
    {
        // Ensure the form is using correct enctype
        $form->setAttribute('enctype', 'multipart/form-data');
    }

    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        return [
            'type'     => FileInput::class,
            'name'     => $this->getName(),
            'required' => false,
        ];
    }
}
