<?php

namespace LaminasTest\Form;

use Laminas\Form\Factory;
use Laminas\Form\FormFactoryAwareTrait;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.4
 */
class FormFactoryAwareTraitTest extends TestCase
{
    public function testSetFormFactory()
    {
        $object = new class {
            use FormFactoryAwareTrait;

            public function getFormFactory()
            {
                return $this->factory;
            }
        };

        $this->assertNull($object->getFormFactory());

        $factory = new Factory;

        $object->setFormFactory($factory);

        $this->assertSame($factory, $object->getFormFactory());
    }
}
