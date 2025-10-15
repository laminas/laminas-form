<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\Factory;
use Laminas\Form\FormFactoryAwareTrait;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;

#[RequiresPhp('5.4')]
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

        self::assertNull($object->getFormFactory());

        $factory = new Factory();

        $object->setFormFactory($factory);

        self::assertSame($factory, $object->getFormFactory());
    }
}
