<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethods as ClassMethodsHydrator;

class CreateAddressForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_address');

        $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $address = new AddressFieldset();
        $address->setUseAsBaseFieldset(true);
        $this->add($address);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit'
            ]
        ]);
    }
}
