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
use LaminasTest\Form\TestAsset\Entity\Category;

class CategoryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('category');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Category());

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name of the category'
            ),
            'attributes' => array(
                'required' => 'required'
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
            )
        );
    }
}
