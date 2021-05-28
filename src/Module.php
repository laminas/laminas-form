<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\ModuleManager\Feature\FormElementProviderInterface;
use Laminas\ModuleManager\ModuleManager;

final class Module
{
    /**
     * Return laminas-form configuration for laminas-mvc application.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();
        return [
            'service_manager' => $provider->getDependencyConfig(),
            'view_helpers'    => $provider->getViewHelperConfig(),
        ];
    }

    /**
     * Register a specification for the FormElementManager with the ServiceListener.
     */
    public function init(ModuleManager $moduleManager): void
    {
        $event           = $moduleManager->getEvent();
        $container       = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'FormElementManager',
            'form_elements',
            FormElementProviderInterface::class,
            'getFormElementConfig'
        );
    }
}
