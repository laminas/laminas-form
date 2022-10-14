<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\Element\Number;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormElementManagerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class FormElementManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new FormElementManagerFactory();
        $elements  = $factory($container, FormElementManager::class);
        self::assertInstanceOf(FormElementManager::class, $elements);
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $element   = $this->createMock(ElementInterface::class);
        $factory   = new FormElementManagerFactory();
        $elements  = $factory($container, FormElementManager::class, [
            'services' => [
                'test' => $element,
            ],
        ]);
        self::assertSame($element, $elements->get('test'));
    }

    public function testConfiguresFormElementsServicesWhenFound(): void
    {
        $element = $this->createMock(ElementInterface::class);
        $config  = [
            'form_elements' => [
                'aliases'   => [
                    'test' => Number::class,
                ],
                'factories' => [
                    'test-too' => static fn($container): ElementInterface => $element,
                ],
            ],
        ];

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', true],
            ]);

        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class);

        self::assertInstanceOf(FormElementManager::class, $elements);
        self::assertTrue($elements->has('test'));
        self::assertInstanceOf(Number::class, $elements->get('test'));
        self::assertTrue($elements->has('test-too'));
        self::assertSame($element, $elements->get('test-too'));
    }

    public function testDoesNotConfigureFormElementsServicesWhenServiceListenerPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('ServiceListener')
            ->willReturn(true);
        $container->expects(self::never())
            ->method('get');

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class);

        self::assertInstanceOf(FormElementManager::class, $elements);
        self::assertFalse($elements->has('test'));
        self::assertFalse($elements->has('test-too'));
    }

    public function testDoesNotConfigureFormElementsServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', false],
            ]);
        $container->expects(self::never())
            ->method('get');

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class);

        self::assertInstanceOf(FormElementManager::class, $elements);
    }

    public function testDoesNotConfigureFormElementServicesWhenConfigServiceDoesNotContainFormElementsConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                ['ServiceListener', false],
                ['config', true],
            ]);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['foo' => 'bar']);

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class);

        self::assertInstanceOf(FormElementManager::class, $elements);
        self::assertFalse($elements->has('foo'));
    }
}
