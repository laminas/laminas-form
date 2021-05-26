<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

use function class_exists;

class CityFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('city');
        $this
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                    ? new ClassMethodsHydrator(false)
                    : new ClassMethods(false)
            )
            ->setObject(new Entity\City());

        $name = new Element('name', ['label' => 'Name of the city']);
        $name->setAttribute('type', 'text');

        $zipCode = new Element('zipCode', ['label' => 'ZipCode of the city']);
        $zipCode->setAttribute('type', 'text');

        $country = new CountryFieldset();
        $country->setLabel('Country');

        $this->add($name);
        $this->add($zipCode);
        $this->add($country);
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
            'name'    => [
                'required' => true,
            ],
            'zipCode' => [
                'required' => true,
            ],
        ];
    }
}
