<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\StringTrim;
use Laminas\Form\Fieldset;
use Laminas\I18n\Validator\Alnum;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class FieldsetWithInputFilter extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @return array[]
     */
    public function getInputFilterSpecification()
    {
        return [
            'foo' => [
                'required'   => true,
                'filters'    => [
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => Alnum::class],
                ],
            ],
            'bar' => [
                'required' => false,
                'filters'  => [
                    ['name' => StringTrim::class],
                ],
            ],
        ];
    }
}
