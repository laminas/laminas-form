<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\Exception\InvalidElementException;
use Laminas\Form\Factory;
use Laminas\Form\Form;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;

/**
 * @group      Laminas_Form
 */
class FormElementManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormElementManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new FormElementManager(new ServiceManager());
    }

    public function testInjectToFormFactoryAware()
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    /**
     * @group 3735
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
        $this->setExpectedException($this->getInvalidServiceException());
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException($this->getInvalidServiceException());
        $this->manager->get('test');
    }

    protected function getInvalidServiceException()
    {
        if (method_exists($this->manager, 'configure')) {
            return InvalidServiceException::class;
        }
        return InvalidElementException::class;
    }

    public function testStringCreationOptions()
    {
        $args = 'foo';
        $element = $this->manager->get('element', $args);
        $this->assertEquals('foo', $element->getName(), 'The argument is string');
    }

    public function testArrayCreationOptions()
    {
        $args = [
            'name' => 'foo',
            'options' => [
                'label' => 'bar'
            ],
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('foo', $element->getName(), 'Specified name in array[name]');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array[options]');
    }

    public function testOptionsCreationOptions()
    {
        $args = [
            'label' => 'bar'
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('element', $element->getName(), 'Invokable CNAME');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array');
    }

    public function testArrayOptionsCreationOptions()
    {
        $args = [
            'options' => [
                'label' => 'bar'
            ],
        ];
        $element = $this->manager->get('element', $args);
        $this->assertEquals('element', $element->getName(), 'Invokable CNAME');
        $this->assertEquals('bar', $element->getLabel(), 'Specified options in array[options]');
    }

    /**
     * @group 6132
     */
    public function testSharedFormElementsAreNotInitializedMultipleTimes()
    {
        $element = $this->getMock('Laminas\Form\Element', ['init']);

        $element->expects($this->once())->method('init');

        $this->manager->setFactory('sharedElement', function () use ($element) {
            return $element;
        });

        $this->manager->setShared('sharedElement', true);

        $this->manager->get('sharedElement');
        $this->manager->get('sharedElement');
    }
}
