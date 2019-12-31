<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;

class OrphansForm extends Form
{
    public function __construct()
    {
        parent::__construct('orphans');

        $this->setAttribute('method', 'post')
            ->setBindOnValidate(self::BIND_ON_VALIDATE)
            ->setInputFilter(new InputFilter());

        //adds a collection of 2
        $this->add(
            [
                'type' => '\Laminas\Form\Element\Collection',
                'name' => 'test',
                'options' => [
                    'use_as_base_fieldset' => true,
                    'count' => 2,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => [
                        'type' => '\LaminasTest\Form\TestAsset\OrphansFieldset'
                    ],
                ]
            ]
        );

        $this->add(
            [
                'name' => 'submit',
                'attributes' => [
                    'type' => 'submit',
                    'value' => 'Send'
                ]
            ]
        );

        $this->setValidationGroup(
            [
                'test' => [
                    'name',
                ],
            ]
        );
    }
}
