<?php

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
    /** @var EventManagerInterface */
    protected $events;

    /** @var Factory */
    protected $formFactory;

    /** @var object */
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
     *
     * @return $this
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers([
            self::class,
            static::class,
        ]);
        (new ElementAnnotationsListener())->attach($events);
        (new FormAnnotationsListener())->attach($events);
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve form factory
     *
     * Lazy-loads the default form factory if none is currently set.
     *
     * @return Factory
     */
    public function getFormFactory()
    {
        if ($this->formFactory) {
            return $this->formFactory;
        }

        $this->formFactory = new Factory();
        return $this->formFactory;
    }

    /**
     * Get event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Creates and returns a form specification for use with a factory
     *
     * @param  string|object $entity Either an instance or a valid class name for an entity
     * @throws Exception\InvalidArgumentException if $entity is not an object or class name
     * @return ArrayObject
     */
    public function getFormSpecification($entity)
    {
        if (! is_object($entity)) {
            if (
                (is_string($entity) && (! class_exists($entity))) // non-existent class
                || (! is_string($entity)) // not an object or string
            ) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s expects an object or valid class name; received "%s"',
                    __METHOD__,
                    var_export($entity, 1)
                ));
            }
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
     * @param string|object $entity
     * @return array
     */
    abstract protected function getFormSpecificationInternal($entity): array;

    /**
     * Create a form from an object.
     *
     * @param  string|object $entity
     * @return FormInterface
     */
    public function createForm($entity)
    {
        $formSpec    = ArrayUtils::iteratorToArray($this->getFormSpecification($entity));
        $formFactory = $this->getFormFactory();
        return $formFactory->createForm($formSpec);
    }

    /**
     * Get the entity used to construct the form.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Configure the form specification from annotations
     *
     * @param  AnnotationCollection $annotations
     * @param  ReflectionClass $reflection
     * @param  ArrayObject $formSpec
     * @param  ArrayObject $filterSpec
     * @return void
     * @triggers discoverName
     * @triggers configureForm
     */
    protected function configureForm($annotations, $reflection, $formSpec, $filterSpec)
    {
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
     * @param  AnnotationCollection $annotations
     * @param  ReflectionProperty $reflection
     * @param  ArrayObject $formSpec
     * @param  ArrayObject $filterSpec
     * @return void
     * @triggers checkForExclude
     * @triggers discoverName
     * @triggers configureElement
     */
    protected function configureElement($annotations, $reflection, $formSpec, $filterSpec)
    {
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

        $elementSpec = $params['elementSpec'];
        $type        = $elementSpec['spec']['type'] ?? Element::class;

        // Compose as a fieldset or an element, based on specification type.
        // If preserve defined order is true, all elements are composed as elements to keep their ordering
        if (! $this->preserveDefinedOrder() && is_subclass_of($type, FieldsetInterface::class)) {
            if (! isset($formSpec['fieldsets'])) {
                $formSpec['fieldsets'] = [];
            }
            $formSpec['fieldsets'][] = $elementSpec;
        } else {
            if (! isset($formSpec['elements'])) {
                $formSpec['elements'] = [];
            }
            $formSpec['elements'][] = $elementSpec;
        }
    }

    /**
     * @param bool $preserveDefinedOrder
     * @return $this
     */
    public function setPreserveDefinedOrder($preserveDefinedOrder)
    {
        $this->preserveDefinedOrder = (bool) $preserveDefinedOrder;
        return $this;
    }

    /**
     * @return bool
     */
    public function preserveDefinedOrder()
    {
        return $this->preserveDefinedOrder;
    }

    /**
     * Discover the name of the given form or element
     *
     * @param  AnnotationCollection $annotations
     * @param  Reflector $reflection
     * @return string
     */
    protected function discoverName($annotations, $reflection)
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
            static function ($r) {
                return is_string($r) && ! empty($r);
            },
            $event
        );
        // @codingStandardsIgnoreEnd

        return $results->last();
    }

    /**
     * Determine if an element is marked to exclude from the definitions
     *
     * @param  AnnotationCollection $annotations
     * @return true|false
     */
    protected function checkForExclude($annotations)
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(['annotations' => $annotations]);

        // @codingStandardsIgnoreStart
        $results = $this->getEventManager()->triggerEventUntil(
            static function ($r) {
                return true === $r;
            },
            $event
        );
        // @codingStandardsIgnoreEnd

        return (bool) $results->last();
    }
}
