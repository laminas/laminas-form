<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\Collection;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
use LaminasTest\Form\TestAsset\CategoryFieldset;
use LaminasTest\Form\TestAsset\CountryFieldset;
use LaminasTest\Form\TestAsset\Entity\Country;
use LaminasTest\Form\TestAsset\Entity\Product;

class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('product');
        $this
            ->setHydrator(new ClassMethodsHydrator())
            ->setObject(new Product());

        $this->add([
            'name'       => 'name',
            'options'    => [
                'label' => 'Name of the product',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'price',
            'options'    => [
                'label' => 'Price of the product',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type'    => Collection::class,
            'name'    => 'categories',
            'options' => [
                'label'          => 'Please choose categories for this product',
                'count'          => 2,
                'target_element' => [
                    'type' => CategoryFieldset::class,
                ],
            ],
        ]);

        $this->add([
            'type'     => CountryFieldset::class,
            'name'     => 'made_in_country',
            'object'   => Country::class,
            'hydrator' => ClassMethodsHydrator::class,
            'options'  => [
                'label' => 'Please choose the country',
            ],
        ]);
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
            'name'            => [
                'required' => true,
            ],
            'price'           => [
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'IsFloat',
                    ],
                ],
            ],
            'made_in_country' => [
                'required' => false,
            ],
        ];
    }
}
