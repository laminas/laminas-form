<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class BasicFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('basic_fieldset');

        $field = new Element('field', ['label' => 'Name']);
        $field->setAttribute('type', 'text');
        $this->add($field);

        $nestedFieldset = new NestedFieldset();
        $this->add($nestedFieldset);
    }

    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'field' => [
                'required' => true,
            ],
        ];
    }
}
