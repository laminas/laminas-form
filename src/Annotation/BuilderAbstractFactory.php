<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Form\Factory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

use function is_array;
use function is_subclass_of;
use function sprintf;

final class BuilderAbstractFactory implements AbstractFactoryInterface
{
    /** @var string[] */
    protected $aliases = [
        'FormAnnotationBuilder' => AnnotationBuilder::class,
        'FormAttributeBuilder'  => AttributeBuilder::class,
    ];

    /**
     * @param  string $requestedName
     * @param  null|array $options
     * @throws ServiceNotCreatedException For invalid listener configuration.
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AbstractBuilder
    {
        // resolve aliases used in laminas servicemanager
        if (isset($this->aliases[$requestedName])) {
            $requestedName = $this->aliases[$requestedName];
        }

        // setup a form factory which can use custom form elements
        $annotationBuilder = new $requestedName();
        $eventManager      = $container->get('EventManager');
        $annotationBuilder->setEventManager($eventManager);

        $this->injectFactory($annotationBuilder->getFormFactory(), $container);

        $config = $this->marshalConfig($container);
        if (isset($config['preserve_defined_order'])) {
            $annotationBuilder->setPreserveDefinedOrder($config['preserve_defined_order']);
        }

        $this->injectListeners($config, $eventManager, $container);

        return $annotationBuilder;
    }

    /**
     * @param  string $requestedName
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return isset($this->aliases[$requestedName]) || is_subclass_of($requestedName, AbstractBuilder::class);
    }

    /**
     * Marshal annotation builder configuration, if any.
     *
     * Looks for the `config` service in the container, returning an empty array
     * if not found.
     *
     * If found, checks for a `form_annotation_builder` entry, returning an empty
     * array if not found or not an array.
     *
     * Otherwise, returns the `form_annotation_builder` array.
     *
     * @return array
     */
    private function marshalConfig(ContainerInterface $container): array
    {
        if (! $container->has('config')) {
            return [];
        }

        $config = $container->get('config');
        $config = $config['form_annotation_builder'] ?? [];

        return is_array($config) ? $config : [];
    }

    /**
     * Inject event listeners from configuration, if any.
     *
     * Loops through the 'listeners' array, and:
     *
     * - attempts to fetch it from the container
     * - if the fetched instance is not a `ListenerAggregate`, raises an exception
     * - otherwise attaches it to the event manager
     *
     * @param  array $config
     * @throws ServiceNotCreatedException If any listener is not an event listener aggregate.
     */
    private function injectListeners(array $config, EventManagerInterface $events, ContainerInterface $container): void
    {
        if (! isset($config['listeners'])) {
            return;
        }

        foreach ($config['listeners'] as $listenerName) {
            $listener = $container->get($listenerName);

            if (! $listener instanceof ListenerAggregateInterface) {
                throw new ServiceNotCreatedException(sprintf('Invalid event listener (%s) provided', $listenerName));
            }

            $listener->attach($events);
        }
    }

    /**
     * Inject the annotation builder's factory instance with the FormElementManager.
     *
     * Also injects the factory with the InputFilterManager if present.
     */
    private function injectFactory(Factory $factory, ContainerInterface $container): void
    {
        $factory->setFormElementManager($container->get('FormElementManager'));

        if ($container->has('InputFilterManager')) {
            $inputFilters = $container->get('InputFilterManager');
            $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
        }
    }
}
