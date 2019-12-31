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
use LaminasTest\Form\TestAsset\Entity\Product;

class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('product');
        $this
            ->setHydrator(
                class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
            )
            ->setObject(new Product());

        $this->add([
            'name' => 'name',
            'options' => [
                'label' => 'Name of the product'
            ],
            'attributes' => [
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name' => 'price',
            'options' => [
                'label' => 'Price of the product'
            ],
            'attributes' => [
                'required' => 'required'
            ]
        ]);

        $this->add([
            'type' => 'Laminas\Form\Element\Collection',
            'name' => 'categories',
            'options' => [
                'label' => 'Please choose categories for this product',
                'count' => 2,
                'target_element' => [
                    'type' => 'LaminasTest\Form\TestAsset\CategoryFieldset'
                ]
            ]
        ]);

        $this->add([
            'type' => 'LaminasTest\Form\TestAsset\CountryFieldset',
            'name' => 'made_in_country',
            'object' => 'LaminasTest\Form\TestAsset\Entity\Country',
            'hydrator' => class_exists(ClassMethodsHydrator::class)
                ? ClassMethodsHydrator::class
                : ClassMethods::class,
            'options' => [
                'label' => 'Please choose the country',
            ]
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
            'name' => [
                'required' => true,
            ],
            'price' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'IsFloat'
                    ]
                ]
            ],
            'made_in_country' => [
                'required' => false,
            ],
        ];
    }
}
