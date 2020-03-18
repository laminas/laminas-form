<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
use LaminasTest\Form\TestAsset\Entity\Phone;

class PhoneFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('phones');

        $this
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
            )
             ->setObject(new Phone());

        $id = new Element\Hidden('id');
        $this->add($id);

        $number = new Element\Text('number');
        $number->setLabel('Number')
               ->setAttribute('class', 'form-control');
        $this->add($number);
    }

    public function getInputFilterSpecification()
    {
        return [
            'number' => [
                'required' => true,
            ],
        ];
    }
}
