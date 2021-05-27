<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;

class CreateAddressForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_address');

        $this
            ->setAttribute('method', 'post')
            ->setHydrator(new ClassMethodsHydrator(false))
            ->setInputFilter(new InputFilter());

        $address = new AddressFieldset();
        $address->setUseAsBaseFieldset(true);
        $this->add($address);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type' => 'submit',
            ],
        ]);
    }
}
