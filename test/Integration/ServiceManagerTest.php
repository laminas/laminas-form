<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\Integration;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\FormElementManagerFactory;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

class ServiceManagerTest extends TestCase
{
    public function testInitInitializerShouldBeCalledAfterAllOtherInitializers()
    {
        // Reproducing the behaviour of a full stack MVC + ModuleManager
        $serviceManagerConfig = new Config([
            'factories' => [
                'FormElementManager' => FormElementManagerFactory::class,
            ],
        ]);

        $serviceManager = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($serviceManager);

        $formElementManagerConfig = new Config([
            'invokables' => [
                'InitializableElement' => TestAsset\InitializableElement::class,
            ],
            'initializers' => [
                TestAsset\DependancyInitializer::class,
            ],
        ]);

        $formElementManager = $serviceManager->get('FormElementManager');
        $formElementManagerConfig->configureServiceManager($formElementManager);

        $element = $formElementManager->get('InitializableElement');

        $this->assertSame(1, $element->dependency);
        $this->assertSame(1, $element->dependencyAtTimeOfInit);
    }
}
