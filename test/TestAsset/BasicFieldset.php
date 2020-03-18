<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class BasicFieldset extends Fieldset implements \Laminas\InputFilter\InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('basic_fieldset');

        $field = new Element('field', ['label' => 'Name']);
        $field->setAttribute('type', 'text');
        $this->add($field);

        $nestedFieldset = new NestedFieldset();
        $this->add($nestedFieldset);
    }


    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'field' => [
                'required' => true,
            ],
        ];
    }
}
