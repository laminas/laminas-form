<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\Element;
use Laminas\Form\ElementFactory;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidElementException;
use Laminas\Form\Factory;
use Laminas\Form\Form;
use Laminas\Form\FormElementManager;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\PluginManagerInterface;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\Form\TestAsset\InvokableForm;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionProperty;
use Throwable;

use function array_pop;
use function array_shift;
use function count;
use function method_exists;
use function strtoupper;

/**
 * @group      Laminas_Form
 */
final class FormElementManagerTest extends TestCase
{
    private FormElementManager $manager;

    protected function setUp(): void
    {
        $this->manager = new FormElementManager(new ServiceManager());
    }

    public function testInjectToFormFactoryAware(): void
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    /**
     * @group issue-3735
     */
    public function testInjectsFormElementManagerToFormComposedByFormFactoryAwareElement(): void
    {
        $factory = new Factory();
        $this->manager->setFactory('my-form', static function ($elements) use ($factory): Form {
            $form = new Form();
            $form->setFormFactory($factory);
            return $form;
        });
        $form = $this->manager->get('my-form');
        $this->assertSame($factory, $form->getFormFactory());
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testRegisteringInvalidElementRaisesException(): void
    {
        $this->expectException($this->getInvalidServiceException());
        /** @psalm-suppress InvalidArgument */
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException(): void
    {
        $this->manager->setInvokableClass('test', static::class);
        $this->expectException($this->getInvalidServiceException());
        $this->manager->get('test');
    }

    /** @return class-string<Throwable> */
    protected function getInvalidServiceException(): string
    {
        if (method_exists($this->manager, 'configure')) {
            return InvalidServiceException::class;
        }
        return InvalidElementException::class;
    }

    public function testArrayCreationOptions(): void
    {
        $args    = [
            'name'    => 'foo',
            'options' => [
                'label' => 'bar',
            ],
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('foo', $element->getName(), 'Specified name in array[name]');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array[options]');
    }

    public function testOptionsCreationOptions(): void
    {
        $args    = [
            'label' => 'bar',
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('element', $element->getName(), 'Invokable CNAME');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array');
    }

    public function testArrayOptionsCreationOptions(): void
    {
        $args    = [
            'options' => [
                'label' => 'bar',
            ],
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('element', $element->getName(), 'Invokable CNAME');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array[options]');
    }

    /**
     * @group issue-6132
     */
    public function testSharedFormElementsAreNotInitializedMultipleTimes(): void
    {
        $element = $this->getMockBuilder(Element::class)
            ->setMethods(['init'])
            ->getMock();

        $element->expects($this->once())->method('init');

        $this->manager->setFactory('sharedElement', static fn(): MockObject => $element);

        $this->manager->setShared('sharedElement', true);

        $this->manager->get('sharedElement');
        $this->manager->get('sharedElement');
    }

    public function testWillInstantiateFormFromInvokable(): void
    {
        $form = $this->manager->get('form');
        $this->assertInstanceof(Form::class, $form);
    }

    /**
     * @group issue-58
     * @group issue-64
     */
    public function testInjectFactoryInitializerShouldBeRegisteredFirst(): void
    {
        // @codingStandardsIgnoreStart
        $initializers = [
            static function () : void {
            },
            static function () : void {
            },
        ];
        // @codingStandardsIgnoreEnd

        $manager = new FormElementManager(new ServiceManager(), [
            'initializers' => $initializers,
        ]);

        $r = new ReflectionProperty($manager, 'initializers');
        $r->setAccessible(true);
        $actual = $r->getValue($manager);

        $this->assertGreaterThan(2, count($actual));
        $first = array_shift($actual);
        $this->assertSame([$manager, 'injectFactory'], $first);
    }

    /**
     * @group issue-58
     * @group issue-64
     */
    public function testCallElementInitInitializerShouldBeRegisteredLast(): void
    {
        // @codingStandardsIgnoreStart
        $initializers = [
            static function () : void {
            },
            static function () : void {
            },
        ];
        // @codingStandardsIgnoreEnd

        $manager = new FormElementManager(new ServiceManager(), [
            'initializers' => $initializers,
        ]);

        $r = new ReflectionProperty($manager, 'initializers');
        $r->setAccessible(true);
        $actual = $r->getValue($manager);

        $this->assertGreaterThan(2, count($actual));
        $last = array_pop($actual);
        $this->assertSame([$manager, 'callElementInit'], $last);
    }

    /**
     * @group issue-62
     */
    public function testAddingInvokableCreatesAliasAndMapsClassToElementFactory(): void
    {
        $this->manager->setInvokableClass('foo', TestAsset\ElementWithFilter::class);

        $r = new ReflectionProperty($this->manager, 'aliases');
        $r->setAccessible(true);
        $aliases = $r->getValue($this->manager);

        $this->assertArrayHasKey('foo', $aliases);
        $this->assertEquals(TestAsset\ElementWithFilter::class, $aliases['foo']);

        $r = new ReflectionProperty($this->manager, 'factories');
        $r->setAccessible(true);
        $factories = $r->getValue($this->manager);

        $this->assertArrayHasKey(TestAsset\ElementWithFilter::class, $factories);
        $this->assertEquals(ElementFactory::class, $factories[TestAsset\ElementWithFilter::class]);
    }

    public function testAllAliasesShouldBeCanonicalized(): void
    {
        if (method_exists($this->manager, 'configure')) {
            $this->markTestSkipped('Check canonicalized makes sense only on v2');
        }

        $r = new ReflectionProperty($this->manager, 'aliases');
        $r->setAccessible(true);
        $aliases = $r->getValue($this->manager);

        foreach ($aliases as $name => $alias) {
            $this->manager->get($name . ' ');
            $this->manager->get(strtoupper($name));
            $this->manager->get($name);
        }

        $this->addToAssertionCount(1);
    }

    public function testOptionsAreSetInInvokableForm(): void
    {
        $options = ['foo' => 'bar'];

        /** @var InvokableForm $form */
        $form = $this->manager->get(InvokableForm::class, $options);

        self::assertInstanceOf(InvokableForm::class, $form);
        self::assertSame('invokableform', $form->getName());
        self::assertSame('bar', $form->getOption('foo'));
    }

    public function testGetHydratorByNameMethodShouldUseHydratorManagerIfExists(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);

        // Hydrator manager
        $hydratorManager = $this->createMock(PluginManagerInterface::class);
        $hydratorManager->method('has')
            ->with('NameOfHydrator')
            ->willReturn(true);
        $hydratorManager->method('get')
            ->with('NameOfHydrator')
            ->willReturn($hydrator);

        // Service container
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')
            ->with(HydratorPluginManager::class)
            ->willReturn(true);
        $container->method('get')
            ->with(HydratorPluginManager::class)
            ->willReturn($hydratorManager);

        $formElementManager = new FormElementManager($container);

        // Test
        $this->assertSame(
            $hydrator,
            $formElementManager->getHydratorFromName('NameOfHydrator')
        );
    }

    public function testGetHydratorByNameMethodShouldUseServiceManagerAsFallback(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);

        // Service container
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')
            ->willReturnMap(
                [
                    [
                        'HydratorManager',
                        false,
                    ],
                    [
                        'NameOfHydrator',
                        true,
                    ],
                ]
            );
        $container->method('get')
            ->with('NameOfHydrator')
            ->willReturn($hydrator);

        $formElementManager = new FormElementManager($container);

        // Test
        $this->assertSame(
            $hydrator,
            $formElementManager->getHydratorFromName('NameOfHydrator')
        );
    }

    public function testGetHydratorByNameMethodShouldThrowExceptionForInvalidName(): void
    {
        // Service container
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')
            ->willReturnMap(
                [
                    [
                        'HydratorManager',
                        false,
                    ],
                    [
                        'NameOfHydrator',
                        false,
                    ],
                ]
            );

        $formElementManager = new FormElementManager($container);

        // Test
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Expects string hydrator name to be a valid class name; received "NameOfHydrator"'
        );

        $formElementManager->getHydratorFromName('NameOfHydrator');
    }
}
