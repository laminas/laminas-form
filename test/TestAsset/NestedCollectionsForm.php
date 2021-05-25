<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

class NestedCollectionsForm extends Form
{
    public function __construct()
    {
        parent::__construct('nestedCollectionsForm');

        $this->add([
            'name'     => 'testFieldset',
            'type'     => Fieldset::class,
            'options'  => [
                'use_as_base_fieldset' => true,
            ],
            'elements' => [
                [
                    'spec' => [
                        'name'    => 'groups',
                        'type'    => Collection::class,
                        'options' => [
                            'target_element' => [
                                'type'     => Fieldset::class,
                                'name'     => 'group',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'type' => Text::class,
                                            'name' => 'name',
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'type'    => Collection::class,
                                            'name'    => 'items',
                                            'options' => [
                                                'target_element' => [
                                                    'type'     => Fieldset::class,
                                                    'name'     => 'item',
                                                    'elements' => [
                                                        [
                                                            'spec' => [
                                                                'type' => Text::class,
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
