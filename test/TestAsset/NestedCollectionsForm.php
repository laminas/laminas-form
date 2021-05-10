<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;

class NestedCollectionsForm extends Form
{
    public function __construct()
    {
        parent::__construct('nestedCollectionsForm');

        $this->add([
            'name' => 'testFieldset',
            'type' => 'Laminas\Form\Fieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ],
            'elements' => [
                [
                    'spec' => [
                        'name' => 'groups',
                        'type' => 'Laminas\Form\Element\Collection',
                        'options' => [
                            'target_element' => [
                                'type' => 'Laminas\Form\Fieldset',
                                'name' => 'group',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'type' => 'Laminas\Form\Element\Text',
                                            'name' => 'name',
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'type' => 'Laminas\Form\Element\Collection',
                                            'name' => 'items',
                                            'options' => [
                                                'target_element' => [
                                                    'type' => 'Laminas\Form\Fieldset',
                                                    'name' => 'item',
                                                    'elements' => [
                                                        [
                                                            'spec' => [
                                                                'type' => 'Laminas\Form\Element\Text',
                                                                'name' => 'itemId',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->setValidationGroup([
            'testFieldset' => [
                'groups' => [
                    'name',
                    'items' => [
                        'itemId',
                    ],
                ],
            ],
        ]);
    }
}
