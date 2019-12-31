<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\Form\Form;

class NestedCollectionsForm extends Form
{
    public function __construct()
    {
        parent::__construct('nestedCollectionsForm');

        $this->add(array(
            'name' => 'testFieldset',
            'type' => 'Laminas\Form\Fieldset',
            'options' => array(
                 'use_as_base_fieldset' => true,
             ),
            'elements' => array(
                array(
                    'spec' => array(
                        'name' => 'groups',
                        'type' => 'Laminas\Form\Element\Collection',
                        'options' => array(
                            'target_element' => array(
                                'type' => 'Laminas\Form\Fieldset',
                                'name' => 'group',
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'type' => 'Laminas\Form\Element\Text',
                                            'name' => 'name',
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'type' => 'Laminas\Form\Element\Collection',
                                            'name' => 'items',
                                            'options' => array(
                                                'target_element' => array(
                                                    'type' => 'Laminas\Form\Fieldset',
                                                    'name' => 'item',
                                                    'elements' => array(
                                                        array(
                                                            'spec' => array(
                                                                'type' => 'Laminas\Form\Element\Text',
                                                                'name' => 'itemId',
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->setValidationGroup(array(
            'testFieldset' => array(
                'groups' => array(
                    'name',
                    'items' => array(
                        'itemId'
                    )
                ),
            )
        ));
    }
}
