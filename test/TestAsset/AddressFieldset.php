<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

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

        $street = new \Laminas\Form\Element('street', ['label' => 'Street']);
        $street->setAttribute('type', 'text');

        $city = new CityFieldset;
        $city->setLabel('City');

        $this->add($street);
        $this->add($city);

        $phones = new \Laminas\Form\Element\Collection('phones');
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
            ]
        ];
    }
}
