<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethods as ClassMethodsHydrator;
use LaminasTest\Form\TestAsset\Entity\Phone;

class PhoneFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('phones');

        $this->setHydrator(new ClassMethodsHydrator)
             ->setObject(new Phone());

        $id = new Element\Hidden('id');
        $this->add($id);

        $number = new Element\Text('number');
        $number->setLabel('Number')
               ->setAttribute('class', 'form-control');
        $this->add($number);
    }
}
