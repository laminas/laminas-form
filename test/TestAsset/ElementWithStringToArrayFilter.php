<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;

class ElementWithStringToArrayFilter extends Element implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => 'LaminasTest\Form\TestAsset\StringToArrayFilter'],
            ],
        ];
    }
}
