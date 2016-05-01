<?php
/**
 * @link      http://github.com/zendframework/zend-form for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormElementManagerFactory implements FactoryInterface
{
    /**
     * zend-servicemanager v2 support for invocation options.
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
     * zend-servicemanager v2 support for invocation options.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    /**
     * Are we running under zend-servicemanager v3?
     *
     * @return bool
     */
    private function isV3Container()
    {
        return method_exists(AbstractPluginManager::class, 'configure');
    }
}
