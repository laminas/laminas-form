<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Validator;

class CustomForm extends Form
{
    public function __construct()
    {
        parent::__construct('test_form');

        $this
            ->setAttribute('method', 'post')
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
            );

        $field1 = new Element('name', ['label' => 'Name']);
        $field1->setAttribute('type', 'text');
        $this->add($field1);

        $field2 = new Element('email', ['label' => 'Email']);
        $field2->setAttribute('type', 'text');
        $this->add($field2);

        $this->add([
            'name' => 'csrf',
            'type' => 'Laminas\Form\Element\Csrf',
            'attributes' => [
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
                'filters'  => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
            ],
            'email' => [
                'required' => true,
                'filters'  => [
                    ['name' => 'Laminas\Filter\StringTrim'],
                ],
                'validators' => [
                    new Validator\EmailAddress(),
                ],
            ],
        ];
    }
}
