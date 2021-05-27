<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Generator;
use Interop\Container\ContainerInterface;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\Form\TestAsset\ArgumentRecorder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ElementFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function validCreationOptions(): Generator
    {
        yield 'array' => [['key' => 'value'], ['key' => 'value']];
        yield 'empty-array' => [[], []];
        yield 'null' => [null, []];
    }

    /**
     * @dataProvider validCreationOptions
     * @param mixed $creationOptions
     * @param array $expectedValue
     */
    public function testValidCreationOptions($creationOptions, array $expectedValue): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class)
            ->willImplement(ContainerInterface::class)
            ->reveal();

        $factory = new ElementFactory();
        $result  = $factory->__invoke($container, ArgumentRecorder::class, $creationOptions);
        $this->assertInstanceOf(ArgumentRecorder::class, $result);
        $this->assertSame(['argumentrecorder', $expectedValue], $result->args);
    }
}
