<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Laminas\Validator;

class CustomForm extends Form
{
    public function __construct()
    {
        parent::__construct('test_form');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator());

        $field1 = new Element('name', array('label' => 'Name'));
        $field1->setAttribute('type', 'text');
        $this->add($field1);

        $field2 = new Element('email', array('label' => 'Email'));
        $field2->setAttribute('type', 'text');
        $this->add($field2);

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Laminas\Form\Element\Csrf',
            'attributes' => array(
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit'
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Laminas\Filter\StringTrim'),
                ),
            ),
            'email' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Laminas\Filter\StringTrim'),
                ),
                'validators' => array(
                    new Validator\EmailAddress(),
                ),
            ),
        );
    }
}
