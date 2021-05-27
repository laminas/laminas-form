<?php

namespace LaminasTest\Form;

use Interop\Container\ContainerInterface;
use Laminas\Form\Element\Number;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FormElementManagerFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory   = new FormElementManagerFactory();

        $elements = $factory($container, FormElementManager::class);
        $this->assertInstanceOf(FormElementManager::class, $elements);

        // $this->assertAttributeSame($container, 'creationContext', $elements);
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $element   = $this->prophesize(ElementInterface::class)->reveal();

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class, [
            'services' => [
                'test' => $element,
            ],
        ]);
        $this->assertSame($element, $elements->get('test'));
    }

    public function testConfiguresFormElementsServicesWhenFound(): void
    {
        $element = $this->prophesize(ElementInterface::class)->reveal();
        $config  = [
            'form_elements' => [
                'aliases'   => [
                    'test' => Number::class,
                ],
                'factories' => [
                    'test-too' => function ($container) use ($element): ElementInterface {
                        return $element;
                    },
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container->reveal(), 'FormElementManager');

        $this->assertInstanceOf(FormElementManager::class, $elements);
        $this->assertTrue($elements->has('test'));
        $this->assertInstanceOf(Number::class, $elements->get('test'));
        $this->assertTrue($elements->has('test-too'));
        $this->assertSame($element, $elements->get('test-too'));
    }

    public function testDoesNotConfigureFormElementsServicesWhenServiceListenerPresent(): void
    {
        $element = $this->prophesize(ElementInterface::class)->reveal();
        $config  = [
            'form_elements' => [
                'aliases'   => [
                    'test' => Number::class,
                ],
                'factories' => [
                    'test-too' => function ($container) use ($element): ElementInterface {
                        return $element;
                    },
                ],
            ],
        ];

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(true);
        $container->has('config')->shouldNotBeCalled();
        $container->get('config')->shouldNotBeCalled();

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container->reveal(), 'FormElementManager');

        $this->assertInstanceOf(FormElementManager::class, $elements);
        $this->assertFalse($elements->has('test'));
        $this->assertFalse($elements->has('test-too'));
    }

    public function testDoesNotConfigureFormElementsServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(false);
        $container->get('config')->shouldNotBeCalled();

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container->reveal(), 'FormElementManager');

        $this->assertInstanceOf(FormElementManager::class, $elements);
    }

    public function testDoesNotConfigureFormElementServicesWhenConfigServiceDoesNotContainFormElementsConfig(): void
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $container->has('ServiceListener')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn(['foo' => 'bar']);
        $container->has('MvcTranslator')->willReturn(false); // necessary due to default initializers

        $factory  = new FormElementManagerFactory();
        $elements = $factory($container->reveal(), 'FormElementManager');

        $this->assertInstanceOf(FormElementManager::class, $elements);
        $this->assertFalse($elements->has('foo'));
    }
}
