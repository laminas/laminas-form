<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * @psalm-type Payload = array{
 *     street: non-empty-string,
 *     phones: list<array{id: mixed, number: non-empty-string}>,
 *     city: array{
 *         name: non-empty-string,
 *         zipCode: non-empty-string,
 *         country: array{
 *             name: non-empty-string,
 *             continent: non-empty-string,
 *         },
 *     },
 * }
 * @extends Form<Payload>
 */
class CreateAddressForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_address');

        $this
            ->setAttribute('method', 'post')
            ->setHydrator(new ClassMethodsHydrator(false));

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
