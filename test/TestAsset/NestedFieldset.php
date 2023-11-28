<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class NestedFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('nested_fieldset');

        $field = new Element('anotherField', ['label' => 'Name']);
        $field->setAttribute('type', 'text');

        $this->add($field);
    }

    /** @inheritDoc */
    public function getInputFilterSpecification()
    {
        return [
            'anotherField' => [
                'required' => true,
            ],
        ];
    }
}
