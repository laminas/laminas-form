<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\Hydrator\ArraySerializable;
use Laminas\InputFilter\InputFilterProviderInterface;
use LaminasTest\Form\TestAsset\Entity\Orphan;

class OrphansFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ArraySerializable())
                ->setObject(new Orphan());

        $this->add(array(
                        'name' => 'name',
                        'options' => array('label' => 'Name field'),
                   ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => false,
                'filters' => array(),
                'validators' => array(),
            )
        );
    }
}
