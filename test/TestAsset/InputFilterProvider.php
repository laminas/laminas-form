<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class InputFilterProvider extends Form implements InputFilterProviderInterface
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
