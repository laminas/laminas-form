<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\Integration\TestAsset;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DependencyInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param \Zend\Form\Element\ElementInterface $instance
     * @return void
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (! property_exists($instance, 'dependency')) {
            return;
        }

        $instance->dependency = 1;
    }

    /**
     * @param \Zend\Form\Element\ElementInterface $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, $instance);
    }
}
