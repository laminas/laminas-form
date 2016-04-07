<?php
/**
 * @link      http://github.com/zendframework/zend-form for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\ElementInterface;
use Zend\Form\FormElementManager;
use Zend\Form\FormElementManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormElementManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new FormElementManagerFactory();

        $elements = $factory($container, FormElementManagerFactory::class);
        $this->assertInstanceOf(FormElementManager::class, $elements);

        if (method_exists($elements, 'configure')) {
            // zend-servicemanager v3
            $this->assertAttributeSame($container, 'creationContext', $elements);
        } else {
            // zend-servicemanager v2
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
        $elements = $factory($container, FormElementManagerFactory::class, [
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
