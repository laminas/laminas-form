<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Validator;

use function class_exists;

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
            'name'       => 'csrf',
            'type'       => Csrf::class,
            'attributes' => [],
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type' => 'submit',
            ],
        ]);
    }

    /**
     * @return array[]
     */
    public function getInputFilterSpecification()
    {
        return [
            'name'  => [
                'required' => true,
                'filters'  => [
                    ['name' => StringTrim::class],
                ],
            ],
            'email' => [
                'required'   => true,
                'filters'    => [
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    new Validator\EmailAddress(),
                ],
            ],
        ];
    }
}
