<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class InputFilterProviderWithFieldset extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'foo',
            'options' => array(
                'label' => 'Foo'
            ),
        ));

        $this->add(new BasicFieldset());
    }

    public function getInputFilterSpecification()
    {
        return array(
            'foo' => array(
                'required' => true,
            )
        );
    }
}
