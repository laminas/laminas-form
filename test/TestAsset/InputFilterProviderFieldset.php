<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class InputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name'    => 'foo',
            'options' => [
                'label' => 'Foo',
            ],
        ]);

        $this->add(new BasicFieldset());
    }

    public function getInputFilterSpecification()
    {
        return [
            'foo' => [
                'required' => true,
            ],
        ];
    }
}
