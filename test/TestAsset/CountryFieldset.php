<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

class CountryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('country');
        $this
            ->setHydrator(new ClassMethodsHydrator())
            ->setObject(new Entity\Country());

        $name = new Element('name', ['label' => 'Name of the country']);
        $name->setAttribute('type', 'text');

        $continent = new Element('continent', ['label' => 'Continent of the city']);
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
            'name'      => [
                'required' => true,
            ],
            'continent' => [
                'required' => true,
            ],
        ];
    }
}
