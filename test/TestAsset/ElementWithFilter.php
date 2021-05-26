<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\I18n\Validator\Alnum;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\NotEmpty;

class ElementWithFilter extends Element implements InputProviderInterface
{
    /**
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name'       => $this->getName(),
            'required'   => true,
            'filters'    => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                ['name' => Alnum::class],
            ],
        ];
    }
}
