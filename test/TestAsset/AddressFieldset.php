<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

use function class_exists;

class AddressFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('address');
        $this
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                    ? new ClassMethodsHydrator(false)
                    : new ClassMethods(false)
            )
            ->setObject(new Entity\Address());

        $street = new Element('street', ['label' => 'Street']);
        $street->setAttribute('type', 'text');

        $city = new CityFieldset();
        $city->setLabel('City');

        $this->add($street);
        $this->add($city);

        $phones = new Element\Collection('phones');
        $phones->setLabel('Phone numbers')
               ->setOptions([
                   'count'          => 2,
                   'allow_add'      => true,
                   'allow_remove'   => true,
                   'target_element' => new PhoneFieldset(),
               ]);
        $this->add($phones);
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
            'street' => [
                'required' => true,
            ],
        ];
    }
}
