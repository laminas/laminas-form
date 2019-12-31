<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Interop\Container\ContainerInterface;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit_Framework_TestCase as TestCase;

class FormElementManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new FormElementManagerFactory();

        $elements = $factory($container, FormElementManager::class);
        $this->assertInstanceOf(FormElementManager::class, $elements);

        if (method_exists($elements, 'configure')) {
            // laminas-servicemanager v3
            $this->assertAttributeSame($container, 'creationContext', $elements);
        } else {
            // laminas-servicemanager v2
            $this->assertSame($container, $elements->getServiceLocator());
        }
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $element = $this->prophesize(ElementInterface::class)->reveal();

        $factory = new FormElementManagerFactory();
        $elements = $factory($container, FormElementManager::class, [
            'services' => [
                'test' => $element,
            ],
        ]);
        $this->assertSame($element, $elements->get('test'));
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderServiceManagerV2()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $element = $this->prophesize(ElementInterface::class)->reveal();

        $factory = new FormElementManagerFactory();
        $factory->setCreationOptions([
            'services' => [
                'test' => $element,
            ],
        ]);

        $elements = $factory->createService($container->reveal());
        $this->assertSame($element, $elements->get('test'));
    }
}
