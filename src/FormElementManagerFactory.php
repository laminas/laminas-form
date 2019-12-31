<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FormElementManagerFactory implements FactoryInterface
{
    /**
     * laminas-servicemanager v2 support for invocation options.
     *
     * @param array
     */
    protected $creationOptions;

    /**
     * {@inheritDoc}
     *
     * @return AbstractPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        if ($this->isV3Container()) {
            return new FormElementManager\FormElementManagerV3Polyfill($container, $options ?: []);
        }

        return new FormElementManager\FormElementManagerV2Polyfill($container, $options ?: []);
    }

    /**
     * {@inheritDoc}
     *
     * @return AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this(
            $container,
            $requestedName ?: __NAMESPACE__ . '\FormElementManager',
            $this->creationOptions
        );
    }

    /**
     * laminas-servicemanager v2 support for invocation options.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    /**
     * Are we running under laminas-servicemanager v3?
     *
     * @return bool
     */
    private function isV3Container()
    {
        return method_exists(AbstractPluginManager::class, 'configure');
    }
}
