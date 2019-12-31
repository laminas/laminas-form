<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\Factory;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4
 */
class FormFactoryAwareTraitTest extends TestCase
{
    public function testSetFormFactory()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\FormFactoryAwareTrait');

        $this->assertAttributeEquals(null, 'factory', $object);

        $factory = new Factory;

        $object->setFormFactory($factory);

        $this->assertAttributeEquals($factory, 'factory', $object);
    }
}
