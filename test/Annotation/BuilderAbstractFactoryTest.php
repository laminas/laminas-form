<?php

declare(strict_types=1);

namespace LaminasTest\Form\Annotation;

use Interop\Container\ContainerInterface; // phpcs:disable WebimpressCodingStandard.PHP.CorrectClassNameCase
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Annotation\AttributeBuilder;
use Laminas\Form\Annotation\BuilderAbstractFactory;
use Laminas\Form\Exception\IncompatiblePhpVersionException;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

use const PHP_MAJOR_VERSION;

final class BuilderAbstractFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactoryReturnsAnnotationBuilder(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);

        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('config')->willReturn(false);
        $container->has('InputFilterManager')->willReturn(false);

        $factory = new BuilderAbstractFactory();

        $this->assertTrue($factory->canCreate($container->reveal(), AnnotationBuilder::class));
        $this->assertInstanceOf(
            AnnotationBuilder::class,
            $factory($container->reveal(), AnnotationBuilder::class)
        );

        $this->assertTrue($factory->canCreate($container->reveal(), 'FormAnnotationBuilder'));
        $this->assertInstanceOf(
            AnnotationBuilder::class,
            $factory($container->reveal(), 'FormAnnotationBuilder')
        );
    }

    public function testFactoryReturnsAttributeBuilderForPhp8(): void
    {
        if (PHP_MAJOR_VERSION < 8) {
            $this->markTestSkipped('Can only create attribute builder for PHP >= 8.0.');
        }

        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);

        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('config')->willReturn(false);
        $container->has('InputFilterManager')->willReturn(false);

        $factory = new BuilderAbstractFactory();

        $this->assertTrue($factory->canCreate($container->reveal(), AttributeBuilder::class));
        $this->assertInstanceOf(
            AttributeBuilder::class,
            $factory($container->reveal(), AttributeBuilder::class)
        );

        $this->assertTrue($factory->canCreate($container->reveal(), 'FormAttributeBuilder'));
        $this->assertInstanceOf(
            AttributeBuilder::class,
            $factory($container->reveal(), 'FormAttributeBuilder')
        );
    }

    public function testFactoryReturnsNoAttributeBuilderForPhp7(): void
    {
        if (PHP_MAJOR_VERSION >= 8) {
            $this->markTestSkipped('Should only throw exceptions when creating attribute builder in PHP < 8.0.');
        }

        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);

        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('config')->willReturn(false);
        $container->has('InputFilterManager')->willReturn(false);

        $factory = new BuilderAbstractFactory();

        $this->expectException(IncompatiblePhpVersionException::class);
        $factory($container->reveal(), 'FormAttributeBuilder');
    }

    public function testFactoryCanSetPreserveDefinedOrderFlagFromConfiguration(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);

        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('InputFilterManager')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'form_annotation_builder' => [
                'preserve_defined_order' => true,
            ],
        ]);

        $factory = new BuilderAbstractFactory();
        $builder = $factory($container->reveal(), AnnotationBuilder::class);

        $this->assertTrue($builder->preserveDefinedOrder(), 'Preserve defined order was not set correctly');
    }

    public function testFactoryAllowsAttachingListenersFromConfiguration(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);

        $listener = $this->prophesize(ListenerAggregateInterface::class);
        $listener->attach($events->reveal())->shouldBeCalled();

        $container->has('InputFilterManager')->willReturn(false);
        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'form_annotation_builder' => [
                'listeners' => [
                    'test-listener',
                ],
            ],
        ]);
        $container->get('test-listener')->willReturn($listener->reveal());

        $factory = new BuilderAbstractFactory();
        $factory($container->reveal(), AnnotationBuilder::class);
    }

    public function testFactoryThrowsExceptionWhenAttachingInvalidListeners(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $events    = $this->prophesize(EventManagerInterface::class);
        $listener  = $this->prophesize(stdClass::class);

        $container->get('EventManager')->willReturn($events->reveal());
        $container->get('FormElementManager')->willReturn(new FormElementManager(new ServiceManager()));
        $container->has('InputFilterManager')->willReturn(false);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'form_annotation_builder' => [
                'listeners' => [
                    'test-listener',
                ],
            ],
        ]);
        $container->get('test-listener')->willReturn($listener->reveal());

        $factory = new BuilderAbstractFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('Invalid event listener');
        $factory($container->reveal(), AnnotationBuilder::class);
    }
}
