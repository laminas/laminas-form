<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;

class ElementWithFilter extends Element implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => 'Laminas\Filter\StringTrim'],
            ],
            'validators' => [
                ['name' => 'Laminas\Validator\NotEmpty'],
                ['name' => 'Laminas\I18n\Validator\Alnum'],
            ],
        ];
    }
}
