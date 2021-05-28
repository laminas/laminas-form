<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\Factory;
use Laminas\Form\FormFactoryAwareTrait;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.4
 */
final class FormFactoryAwareTraitTest extends TestCase
{
    public function testSetFormFactory(): void
    {
        $object = new class {
            use FormFactoryAwareTrait;

            /**
             * @return null|Factory
             */
            public function getFormFactory()
            {
                return $this->factory;
            }
        };

        $this->assertNull($object->getFormFactory());

        $factory = new Factory();

        $object->setFormFactory($factory);

        $this->assertSame($factory, $object->getFormFactory());
    }
}
