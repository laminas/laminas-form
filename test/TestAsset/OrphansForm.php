<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\Collection;
use Laminas\Form\Form;

/** @extends Form<array<string, mixed>> */
class OrphansForm extends Form
{
    public function __construct()
    {
        parent::__construct('orphans');

        $this->setAttribute('method', 'post')
            ->setBindOnValidate(self::BIND_ON_VALIDATE);

        //adds a collection of 2
        $this->add(
            [
                'type'    => Collection::class,
                'name'    => 'test',
                'options' => [
                    'use_as_base_fieldset'   => true,
                    'count'                  => 2,
                    'should_create_template' => true,
                    'allow_add'              => true,
                    'target_element'         => [
                        'type' => OrphansFieldset::class,
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'submit',
                'attributes' => [
                    'type'  => 'submit',
                    'value' => 'Send',
                ],
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
