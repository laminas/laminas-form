<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CityFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('city');
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new Entity\City());

        $name = new \Laminas\Form\Element('name', array('label' => 'Name of the city'));
        $name->setAttribute('type', 'text');

        $zipCode = new \Laminas\Form\Element('zipCode', array('label' => 'ZipCode of the city'));
        $zipCode->setAttribute('type', 'text');

        $country = new CountryFieldset;
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
        return array(
            'name' => array(
                'required' => true,
            ),
            'zipCode' => array(
                'required' => true
            )
        );
    }
}
