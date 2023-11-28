<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use ArrayObject;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Form\Element;
use Laminas\Form\Exception;
use Laminas\Form\Factory;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormFactoryAwareInterface;
use Laminas\Form\FormInterface;
use Laminas\Stdlib\ArrayUtils;
use ReflectionClass;
use ReflectionProperty;
use Reflector;

use function assert;
use function class_exists;
use function is_array;
use function is_object;
use function is_string;
use function is_subclass_of;
use function sprintf;
use function var_export;

/**
 * Creates a form and input-filters from an array-based form specification
 */
abstract class AbstractBuilder implements EventManagerAwareInterface, FormFactoryAwareInterface
{
    /** @var null|EventManagerInterface */
    protected $eventManager;

    /** @var null|Factory */
    protected $formFactory;

    /** @var null|class-string|object */
    protected $entity;

    /** @var bool */
    protected $preserveDefinedOrder = false;

    /**
     * Set form factory to use when building form from annotations
     *
     * @return $this
     */
    public function setFormFactory(Factory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * Set event manager instance
     */
    public function setEventManager(EventManagerInterface $eventManager): void
    {
        $eventManager->setIdentifiers([
            self::class,
            static::class,
        ]);
        (new ElementAnnotationsListener())->attach($eventManager);
        (new FormAnnotationsListener())->attach($eventManager);
        $this->eventManager = $eventManager;
    }

    /**
     * Retrieve form factory
     *
     * Lazy-loads the default form factory if none is currently set.
     */
    public function getFormFactory(): Factory
    {
        if ($this->formFactory) {
            return $this->formFactory;
        }

        $this->formFactory = new Factory();
        return $this->formFactory;
    }

    /**
     * Get event manager
     */
    public function getEventManager(): EventManagerInterface
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
            assert(null !== $this->eventManager);
        }
        return $this->eventManager;
    }

    /**
     * Creates and returns a form specification for use with a factory
     *
     * @param  class-string|object $entity Either an instance or a valid class name for an entity
     * @throws Exception\InvalidArgumentException If $entity is not an object or class name.
     */
    public function getFormSpecification($entity): ArrayObject
    {
        if (! is_object($entity) && ! is_string($entity) || (is_string($entity) && ! class_exists($entity))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object or valid class name; received %s',
                __METHOD__,
                var_export($entity, true)
            ));
        }

        $this->entity            = $entity;
        [$formSpec, $filterSpec] = $this->getFormSpecificationInternal($entity);

        if (! isset($formSpec['input_filter'])) {
            $formSpec['input_filter'] = $filterSpec;
        } elseif (is_array($formSpec['input_filter'])) {
            $formSpec['input_filter'] = ArrayUtils::merge($filterSpec->getArrayCopy(), $formSpec['input_filter']);
        }

        return $formSpec;
    }

    /**
     * Implementation of deriving a form specification from an entity
     *
     * Must return an array with two elements, where the first element is the form specification and the
     * second element is the input filter specification.
     *
     * @param class-string|object $entity
     * @return array{0: ArrayObject, 1: ArrayObject}
     */
    abstract protected function getFormSpecificationInternal($entity): array;

    /**
     * Create a form from an object.
     *
     * @param  class-string|object $entity
     */
    public function createForm($entity): FormInterface
    {
        $formSpec    = ArrayUtils::iteratorToArray($this->getFormSpecification($entity));
        $formFactory = $this->getFormFactory();
        return $formFactory->createForm($formSpec);
    }

    /**
     * Get the entity used to construct the form.
     *
     * @return null|class-string|object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Configure the form specification from annotations
     *
     * @triggers discoverName
     * @triggers configureForm
     */
    protected function configureForm(
        AnnotationCollection $annotations,
        ReflectionClass $reflection,
        ArrayObject $formSpec,
        ArrayObject $filterSpec
    ): void {
        $name                   = $this->discoverName($annotations, $reflection);
        $formSpec['name']       = $name;
        $formSpec['attributes'] = [];
        $formSpec['elements']   = [];
        $formSpec['fieldsets']  = [];

        $events = $this->getEventManager();
        foreach ($annotations as $annotation) {
            $events->trigger(__FUNCTION__, $this, [
                'annotation' => $annotation,
                'name'       => $name,
                'formSpec'   => $formSpec,
                'filterSpec' => $filterSpec,
            ]);
        }
    }

    /**
     * Configure an element from annotations
     *
     * @triggers checkForExclude
     * @triggers discoverName
     * @triggers configureElement
     */
    protected function configureElement(
        AnnotationCollection $annotations,
        ReflectionProperty $reflection,
        ArrayObject $formSpec,
        ArrayObject $filterSpec
    ): void {
        // If the element is marked as exclude, return early
        if ($this->checkForExclude($annotations)) {
            return;
        }

        $events = $this->getEventManager();
        $name   = $this->discoverName($annotations, $reflection);

        $elementSpec = new ArrayObject([
            'flags' => [],
            'spec'  => [
                'name' => $name,
            ],
        ]);
        $inputSpec   = new ArrayObject([
            'name' => $name,
        ]);

        $params = [
            'name'        => $name,
            'elementSpec' => $elementSpec,
            'inputSpec'   => $inputSpec,
            'formSpec'    => $formSpec,
            'filterSpec'  => $filterSpec,
        ];
        foreach ($annotations as $annotation) {
            $params['annotation'] = $annotation;
            $events->trigger(__FUNCTION__, $this, $params);
        }

        // Since "type" is a reserved name in the filter specification,
        // we need to add the specification without the name as the key.
        // In all other cases, though, the name is fine.
        if ($params['inputSpec']->count() > 1) {
            if ($name === 'type') {
                $filterSpec[] = $params['inputSpec'];
            } else {
                $filterSpec[$name] = $params['inputSpec'];
            }
        }

        $type = $params['elementSpec']['spec']['type'] ?? Element::class;

        // Compose as a fieldset or an element, based on specification type.
        // If preserve defined order is true, all elements are composed as elements to keep their ordering
        if (! $this->preserveDefinedOrder() && is_subclass_of($type, FieldsetInterface::class)) {
            if (! isset($formSpec['fieldsets'])) {
                $formSpec['fieldsets'] = [];
            }
            $formSpec['fieldsets'][] = $params['elementSpec'];
        } else {
            if (! isset($formSpec['elements'])) {
                $formSpec['elements'] = [];
            }
            $formSpec['elements'][] = $params['elementSpec'];
        }
    }

    /**
     * @return $this
     */
    public function setPreserveDefinedOrder(bool $preserveDefinedOrder)
    {
        $this->preserveDefinedOrder = $preserveDefinedOrder;
        return $this;
    }

    public function preserveDefinedOrder(): bool
    {
        return $this->preserveDefinedOrder;
    }

    /**
     * Discover the name of the given form or element
     */
    protected function discoverName(AnnotationCollection $annotations, Reflector $reflection): string
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams([
            'annotations' => $annotations,
            'reflection'  => $reflection,
        ]);

        // @codingStandardsIgnoreStart
        $results = $this->getEventManager()->triggerEventUntil(
            static fn(?string $r): bool => $r !== null && $r !== '',
            $event
        );
        // @codingStandardsIgnoreEnd

        return $results->last();
    }

    /**
     * Determine if an element is marked to exclude from the definitions
     *
     * @return true|false
     */
    protected function checkForExclude(AnnotationCollection $annotations): bool
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(['annotations' => $annotations]);

        // @codingStandardsIgnoreStart
        $results = $this->getEventManager()->triggerEventUntil(
            static fn(bool $r): bool => true === $r,
            $event
        );
        // @codingStandardsIgnoreEnd

        return (bool) $results->last();
    }
}
