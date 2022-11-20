<?php

declare(strict_types=1);

namespace LaminasTest\Form\Annotation;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Annotation\AttributeBuilder;
use Laminas\Form\Annotation\BuilderAbstractFactory;
use Laminas\Form\FormElementManager;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class BuilderAbstractFactoryTest extends TestCase
{
    public function testFactoryReturnsAnnotationBuilder(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $events    = $this->createMock(EventManagerInterface::class);

        $container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                ['EventManager', $events],
                [FormElementManager::class, new FormElementManager(new ServiceManager())],
            ]);

        $container->expects(self::atLeast(2))
            ->method('has')
            ->willReturnMap([
                ['config', false],
                [InputFilterPluginManager::class, false],
            ]);

        $factory = new BuilderAbstractFactory();

        self::assertTrue($factory->canCreate($container, AnnotationBuilder::class));
        self::assertInstanceOf(
            AnnotationBuilder::class,
            $factory($container, AnnotationBuilder::class)
        );

        self::assertTrue($factory->canCreate($container, 'FormAnnotationBuilder'));
        self::assertInstanceOf(
            AnnotationBuilder::class,
            $factory($container, 'FormAnnotationBuilder')
        );
    }

    public function testFactoryReturnsAttributeBuilderForPhp8(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $events    = $this->createMock(EventManagerInterface::class);

        $container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                ['EventManager', $events],
                [FormElementManager::class, new FormElementManager(new ServiceManager())],
            ]);

        $container->expects(self::atLeast(2))
            ->method('has')
            ->willReturnMap([
                ['config', false],
                [InputFilterPluginManager::class, false],
            ]);

        $factory = new BuilderAbstractFactory();

        self::assertTrue($factory->canCreate($container, AttributeBuilder::class));
        self::assertInstanceOf(
            AttributeBuilder::class,
            $factory($container, AttributeBuilder::class)
        );

        self::assertTrue($factory->canCreate($container, 'FormAttributeBuilder'));
        self::assertInstanceOf(
            AttributeBuilder::class,
            $factory($container, 'FormAttributeBuilder')
        );
    }

    public function testFactoryCanSetPreserveDefinedOrderFlagFromConfiguration(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $events    = $this->createMock(EventManagerInterface::class);
        $config    = [
            'form_annotation_builder' => [
                'preserve_defined_order' => true,
            ],
        ];

        $container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                ['EventManager', $events],
                [FormElementManager::class, new FormElementManager(new ServiceManager())],
                ['config', $config],
            ]);

        $container->expects(self::atLeast(2))
            ->method('has')
            ->willReturnMap([
                ['config', true],
                [InputFilterPluginManager::class, false],
            ]);

        $factory = new BuilderAbstractFactory();
        $builder = $factory($container, AnnotationBuilder::class);

        self::assertTrue($builder->preserveDefinedOrder(), 'Preserve defined order was not set correctly');
    }

    public function testFactoryAllowsAttachingListenersFromConfiguration(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $events    = $this->createMock(EventManagerInterface::class);
        $listener  = $this->createMock(ListenerAggregateInterface::class);
        $config    = [
            'form_annotation_builder' => [
                'listeners' => [
                    'test-listener',
                ],
            ],
        ];

        $container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                ['EventManager', $events],
                [FormElementManager::class, new FormElementManager(new ServiceManager())],
                ['config', $config],
                ['test-listener', $listener],
            ]);

        $container->expects(self::atLeast(2))
            ->method('has')
            ->willReturnMap([
                ['config', true],
                [InputFilterPluginManager::class, false],
            ]);

        $listener->expects(self::once())
            ->method('attach')
            ->with($events);

        $factory = new BuilderAbstractFactory();
        $factory($container, AnnotationBuilder::class);
    }

    public function testFactoryThrowsExceptionWhenAttachingInvalidListeners(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $events    = $this->createMock(EventManagerInterface::class);
        $config    = [
            'form_annotation_builder' => [
                'listeners' => [
                    'test-listener',
                ],
            ],
        ];

        $container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                ['EventManager', $events],
                [FormElementManager::class, new FormElementManager(new ServiceManager())],
                ['config', $config],
                [
                    'test-listener',
                    new class () {
                    },
                ],
            ]);

        $container->expects(self::atLeast(2))
            ->method('has')
            ->willReturnMap([
                ['config', true],
                [InputFilterPluginManager::class, false],
            ]);

        $factory = new BuilderAbstractFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('Invalid event listener');
        $factory($container, AnnotationBuilder::class);
    }
}
