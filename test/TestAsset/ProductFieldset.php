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
use LaminasTest\Form\TestAsset\Entity\Product;

class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('product');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Product());

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name of the product'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

        $this->add(array(
            'name' => 'price',
            'options' => array(
                'label' => 'Price of the product'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

        $this->add(array(
            'type' => 'Laminas\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'label' => 'Please choose categories for this product',
                'count' => 2,
                'target_element' => array(
                    'type' => 'LaminasTest\Form\TestAsset\CategoryFieldset'
                )
            )
        ));

        $this->add(array(
            'type' => 'LaminasTest\Form\TestAsset\CountryFieldset',
            'name' => 'made_in_country',
            'object' => 'LaminasTest\Form\TestAsset\Entity\Country',
            'hydrator' => 'Laminas\Stdlib\Hydrator\ClassMethods',
            'options' => array(
                'label' => 'Please choose the country',
            )
        ));
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
            'price' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'IsFloat'
                    )
                )
            ),
            'made_in_country' => array(
                'required' => false,
            ),
        );
    }
}
