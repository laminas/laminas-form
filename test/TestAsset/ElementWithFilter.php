<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;

class ElementWithFilter extends Element implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Laminas\Filter\StringTrim'),
            ),
            'validators' => array(
                array('name' => 'Laminas\Validator\NotEmpty'),
                array('name' => 'Laminas\I18n\Validator\Alnum'),
            ),
        );
    }
}
