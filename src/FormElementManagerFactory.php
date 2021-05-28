<?php

declare(strict_types=1);

namespace Laminas\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;

use function is_array;

final class FormElementManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return AbstractPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, ?array $options = null): AbstractPluginManager
    {
        $pluginManager = new FormElementManager($container, $options ?: []);

        // If this is in a laminas-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        // If we do not have a config service, nothing more to do
        if (! $container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        // If we do not have form_elements configuration, nothing more to do
        if (! isset($config['form_elements']) || ! is_array($config['form_elements'])) {
            return $pluginManager;
        }

        // Wire service configuration for forms and elements
        (new Config($config['form_elements']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}
