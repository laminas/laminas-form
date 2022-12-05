<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function is_array;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class FormElementManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     * @param string|null $requestedName
     * @param ServiceManagerConfiguration|null $options
     * @return AbstractPluginManager
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): AbstractPluginManager {
        /** @psalm-var ServiceManagerConfiguration $options */
        $options       = is_array($options) ? $options : [];
        $pluginManager = new FormElementManager($container, $options);

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

        /** @psalm-var ServiceManagerConfiguration $config['form_elements'] */

        // Wire service configuration for forms and elements
        (new Config($config['form_elements']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}
