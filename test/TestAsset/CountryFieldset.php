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

class CountryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('country');
        $this
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                    ? new ClassMethodsHydrator()
                    : new ClassMethods()
            )
            ->setObject(new Entity\Country());

        $name = new \Laminas\Form\Element('name', ['label' => 'Name of the country']);
        $name->setAttribute('type', 'text');

        $continent = new \Laminas\Form\Element('continent', ['label' => 'Continent of the city']);
        $continent->setAttribute('type', 'text');

        $this->add($name);
        $this->add($continent);
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
            'name' => [
                'required' => true,
            ],
            'continent' => [
                'required' => true,
            ],
        ];
    }
}
