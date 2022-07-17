<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\Filter\FilterPluginManager;
use Laminas\Form\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Validator\ValidatorPluginManager;
use Psr\Container\ContainerInterface;

use function is_array;
use function is_string;

final class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    /** @var null|array */
    private $config;

    /** @var string Top-level configuration key indicating forms configuration */
    private string $configKey = 'forms';

    /** @var null|Factory Form factory used to create forms */
    private ?Factory $factory = null;

    /**
     * Create a form (v3)
     *
     * @param string $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FormInterface
    {
        $config  = $this->getConfig($container);
        $config  = $config[$requestedName];
        $factory = $this->getFormFactory($container);

        $this->marshalInputFilter($config, $container, $factory);
        return $factory->createForm($config);
    }

    /**
     * Can we create the requested service? (v3)
     *
     * @param  string             $requestedName
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        // avoid infinite loops when looking up config
        if ($requestedName === 'config') {
            return false;
        }

        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return isset($config[$requestedName])
            && is_array($config[$requestedName])
            && ! empty($config[$requestedName]);
    }

    /**
     * Get forms configuration, if any
     *
     * @return array
     */
    protected function getConfig(ContainerInterface $container): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (! $container->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $container->get('config');
        if (
            ! isset($config[$this->configKey])
            || ! is_array($config[$this->configKey])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }

    /**
     * Retrieve the form factory, creating it if necessary
     */
    protected function getFormFactory(ContainerInterface $container): Factory
    {
        if ($this->factory instanceof Factory) {
            return $this->factory;
        }

        $elements = null;
        if ($container->has(FormElementManager::class)) {
            $elements = $container->get(FormElementManager::class);
        }

        $this->factory = new Factory($elements);
        return $this->factory;
    }

    /**
     * Marshal the input filter into the configuration
     *
     * If an input filter is specified:
     * - if the InputFilterManager is present, checks if it's there; if so,
     *   retrieves it and resets the specification to the instance.
     * - otherwise, pulls the input filter factory from the form factory, and
     *   attaches the FilterManager and ValidatorManager to it.
     *
     * @param array $config
     */
    protected function marshalInputFilter(array &$config, ContainerInterface $container, Factory $formFactory): void
    {
        if (! isset($config['input_filter'])) {
            return;
        }

        if ($config['input_filter'] instanceof InputFilterInterface) {
            return;
        }

        if (
            is_string($config['input_filter'])
            && $container->has(InputFilterPluginManager::class)
        ) {
            $inputFilters = $container->get(InputFilterPluginManager::class);
            if ($inputFilters->has($config['input_filter'])) {
                $config['input_filter'] = $inputFilters->get($config['input_filter']);
                return;
            }
        }

        $inputFilterFactory = $formFactory->getInputFilterFactory();

        $filterChain = $inputFilterFactory->getDefaultFilterChain();
        $filterChain->setPluginManager(
            $container->get(FilterPluginManager::class)
        );

        $validatorChain = $inputFilterFactory->getDefaultValidatorChain();
        $validatorChain->setPluginManager(
            $container->get(ValidatorPluginManager::class)
        );
    }
}
