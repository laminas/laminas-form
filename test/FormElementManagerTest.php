<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\ElementFactory;
use Laminas\Form\Exception\InvalidElementException;
use Laminas\Form\Factory;
use Laminas\Form\Form;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use function array_pop;
use function array_shift;
use function count;
use function get_class;
use function method_exists;
use function strtoupper;

/**
 * @group      Laminas_Form
 */
class FormElementManagerTest extends TestCase
{
    /**
     * @var FormElementManager
     */
    protected $manager;

    protected function setUp(): void
    {
        $this->manager = new FormElementManager(new ServiceManager());
    }

    public function testInjectToFormFactoryAware()
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    /**
     * @group issue-3735
     */
    public function testInjectsFormElementManagerToFormComposedByFormFactoryAwareElement()
    {
        $factory = new Factory();
        $this->manager->setFactory('my-form', function ($elements) use ($factory) {
            $form = new Form();
            $form->setFormFactory($factory);
            return $form;
        });
        $form = $this->manager->get('my-form');
        $this->assertSame($factory, $form->getFormFactory());
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->expectException($this->getInvalidServiceException());
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->expectException($this->getInvalidServiceException());
        $this->manager->get('test');
    }

    protected function getInvalidServiceException()
    {
        if (method_exists($this->manager, 'configure')) {
            return InvalidServiceException::class;
        }
        return InvalidElementException::class;
    }

    public function testArrayCreationOptions()
    {
        $args = [
            'name' => 'foo',
            'options' => [
                'label' => 'bar',
            ],
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('foo', $element->getName(), 'Specified name in array[name]');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array[options]');
    }

    public function testOptionsCreationOptions()
    {
        $args = [
            'label' => 'bar',
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('element', $element->getName(), 'Invokable CNAME');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array');
    }

    public function testArrayOptionsCreationOptions()
    {
        $args = [
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
    public function testSharedFormElementsAreNotInitializedMultipleTimes()
    {
        $element = $this->getMockBuilder('Laminas\Form\Element')
            ->setMethods(['init'])
            ->getMock();

        $element->expects($this->once())->method('init');

        $this->manager->setFactory('sharedElement', function () use ($element) {
            return $element;
        });

        $this->manager->setShared('sharedElement', true);

        $this->manager->get('sharedElement');
        $this->manager->get('sharedElement');
    }

    public function testWillInstantiateFormFromInvokable()
    {
        $form = $this->manager->get('form');
        $this->assertInstanceof(Form::class, $form);
    }

    /**
     * @group issue-58
     * @group issue-64
     */
    public function testInjectFactoryInitializerShouldBeRegisteredFirst()
    {
        // @codingStandardsIgnoreStart
        $initializers = [
            function () {},
            function () {},
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
    public function testCallElementInitInitializerShouldBeRegisteredLast()
    {
        // @codingStandardsIgnoreStart
        $initializers = [
            function () {},
            function () {},
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
    public function testAddingInvokableCreatesAliasAndMapsClassToElementFactory()
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

        if (method_exists($this->manager, 'configure')) {
            $this->assertArrayHasKey(TestAsset\ElementWithFilter::class, $factories);
            $this->assertEquals(ElementFactory::class, $factories[TestAsset\ElementWithFilter::class]);
        } else {
            $this->assertArrayHasKey('laminastestformtestassetelementwithfilter', $factories);
            $this->assertEquals(ElementFactory::class, $factories['laminastestformtestassetelementwithfilter']);
        }
    }

    public function testAllAliasesShouldBeCanonicalized()
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
}
