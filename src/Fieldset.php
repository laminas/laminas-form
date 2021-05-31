<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\Form\Element\Collection;
use Laminas\Hydrator;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\PriorityList;
use ReflectionClass;
use Traversable;

use function array_key_exists;
use function assert;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_object;
use function ltrim;
use function sprintf;

class Fieldset extends Element implements FieldsetInterface
{
    /** @var null|Factory */
    protected $factory;

    /** @var array */
    protected $elements = [];

    /** @var FieldsetInterface[] */
    protected $fieldsets = [];

    /** @var array */
    protected $messages = [];

    /** @var PriorityList */
    protected $iterator;

    /**
     * Hydrator to use with bound object
     *
     * @var null|Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * The object bound to this fieldset, if any
     *
     * @var null|object
     */
    protected $object;

    /**
     * Should this fieldset be used as a base fieldset in the parent form ?
     *
     * @var bool
     */
    protected $useAsBaseFieldset = false;

    /**
     * The class or interface of objects that can be bound to this fieldset.
     *
     * @var null|class-string
     */
    protected $allowedObjectBindingClass;

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, array $options = [])
    {
        $this->iterator = new PriorityList();
        $this->iterator->isLIFO(false);
        parent::__construct($name, $options);
    }

    /**
     * Set options for a fieldset. Accepted options are:
     * - use_as_base_fieldset: is this fieldset use as the base fieldset?
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['use_as_base_fieldset'])) {
            $this->setUseAsBaseFieldset($this->options['use_as_base_fieldset']);
        }

        if (isset($this->options['allowed_object_binding_class'])) {
            $this->setAllowedObjectBindingClass($this->options['allowed_object_binding_class']);
        }

        return $this;
    }

    /**
     * Compose a form factory to use when calling add() with a non-element/fieldset
     *
     * @return $this
     */
    public function setFormFactory(Factory $formFactory)
    {
        $this->factory = $formFactory;
        return $this;
    }

    /**
     * Retrieve composed form factory
     *
     * Lazy-loads one if none present.
     */
    public function getFormFactory(): Factory
    {
        if (null === $this->factory) {
            $this->setFormFactory(new Factory());
            assert(null !== $this->factory);
        }

        return $this->factory;
    }

    /**
     * Add an element or fieldset
     *
     * $flags could contain metadata such as the alias under which to register
     * the element or fieldset, order in which to prioritize it, etc.
     *
     * @todo   Should we detect if the element/fieldset name conflicts?
     * @param  array|Traversable|ElementInterface $elementOrFieldset
     * @param  array                              $flags
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function add($elementOrFieldset, array $flags = [])
    {
        if (
            is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && ! $elementOrFieldset instanceof ElementInterface)
        ) {
            $factory           = $this->getFormFactory();
            $elementOrFieldset = $factory->create($elementOrFieldset);
        }

        if (! $elementOrFieldset instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that $elementOrFieldset be an object implementing %s; received "%s"',
                __METHOD__,
                __NAMESPACE__ . '\ElementInterface',
                is_object($elementOrFieldset) ? get_class($elementOrFieldset) : gettype($elementOrFieldset)
            ));
        }

        $name = $elementOrFieldset->getName();
        if (array_key_exists('name', $flags) && $flags['name'] !== '') {
            $name = $flags['name'];

            // Rename the element or fieldset to the specified alias
            $elementOrFieldset->setName($name);
        }

        if (null === $name || '' === $name) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: element or fieldset provided is not named, and no name provided in flags',
                __METHOD__
            ));
        }
        $order = 0;
        if (array_key_exists('priority', $flags)) {
            $order = $flags['priority'];
        }

        $this->iterator->insert($name, $elementOrFieldset, $order);

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $this->fieldsets[$name] = $elementOrFieldset;
            return $this;
        }

        $this->elements[$name] = $elementOrFieldset;
        return $this;
    }

    /**
     * Does the fieldset have an element/fieldset by the given name?
     */
    public function has(string $elementOrFieldset): bool
    {
        return $this->iterator->get($elementOrFieldset) !== null;
    }

    /**
     * Retrieve a named element or fieldset
     *
     * @return FieldsetInterface|ElementInterface
     */
    public function get(string $elementOrFieldset): ElementInterface
    {
        if (! $this->has($elementOrFieldset)) {
            throw new Exception\InvalidElementException(sprintf(
                'No element by the name of [%s] found in form',
                $elementOrFieldset
            ));
        }
        return $this->iterator->get($elementOrFieldset);
    }

    /**
     * Remove a named element or fieldset
     *
     * @return $this
     */
    public function remove(string $elementOrFieldset)
    {
        if (! $this->has($elementOrFieldset)) {
            return $this;
        }

        $this->iterator->remove($elementOrFieldset);

        if (isset($this->fieldsets[$elementOrFieldset])) {
            unset($this->fieldsets[$elementOrFieldset]);
            return $this;
        }

        unset($this->elements[$elementOrFieldset]);
        return $this;
    }

    /**
     * Set/change the priority of an element or fieldset
     *
     * @return $this
     */
    public function setPriority(string $elementOrFieldset, int $priority)
    {
        $this->iterator->setPriority($elementOrFieldset, $priority);
        return $this;
    }

    /**
     * Retrieve all attached elements
     *
     * Storage is an implementation detail of the concrete class.
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Retrieve all attached fieldsets
     *
     * Storage is an implementation detail of the concrete class.
     */
    public function getFieldsets(): array
    {
        return $this->fieldsets;
    }

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages(iterable $messages)
    {
        foreach ($messages as $key => $messageSet) {
            if (! $this->has((string) $key)) {
                $this->messages[$key] = $messageSet;
                continue;
            }

            $element = $this->get((string) $key);
            $element->setMessages($messageSet);
        }

        return $this;
    }

    /**
     * Get validation error messages, if any
     *
     * Returns a hash of element names/messages for all elements failing
     * validation, or, if $elementName is provided, messages for that element
     * only.
     *
     * @throws Exception\InvalidArgumentException
     */
    public function getMessages(?string $elementName = null): array
    {
        if (null === $elementName) {
            $messages = $this->messages;
            foreach ($this->iterator as $name => $element) {
                $messageSet = $element->getMessages();
                if (
                    empty($messageSet)
                    || (! is_array($messageSet) && ! $messageSet instanceof Traversable)
                ) {
                    continue;
                }
                $messages[$name] = $messageSet;
            }
            return $messages;
        }

        if (! $this->has($elementName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid element name "%s" provided to %s',
                $elementName,
                __METHOD__
            ));
        }

        $element = $this->get($elementName);
        return $element->getMessages();
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     */
    public function prepareElement(FormInterface $form): void
    {
        $name = $this->getName();

        foreach ($this->iterator as $elementOrFieldset) {
            $elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

    /**
     * Recursively populate values of attached elements and fieldsets
     *
     * @param  iterable $data
     * @throws Exception\InvalidArgumentException
     */
    public function populateValues(iterable $data): void
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        foreach ($this->iterator as $name => $elementOrFieldset) {
            $valueExists = array_key_exists($name, $data);

            if ($elementOrFieldset instanceof FieldsetInterface) {
                if ($valueExists && (is_array($data[$name]) || $data[$name] instanceof Traversable)) {
                    $elementOrFieldset->populateValues($data[$name]);
                    continue;
                }

                if ($elementOrFieldset instanceof Element\Collection) {
                    if ($valueExists && null !== $data[$name]) {
                        $elementOrFieldset->populateValues($data[$name]);
                        continue;
                    }

                    /* This ensures that collections with allow_remove don't re-create child
                     * elements if they all were removed */
                    $elementOrFieldset->populateValues([]);
                    continue;
                }
            }

            if ($valueExists) {
                $elementOrFieldset->setValue($data[$name]);
            }
        }
    }

    /**
     * Countable: return count of attached elements/fieldsets
     */
    public function count(): int
    {
        return $this->iterator->count();
    }

    /**
     * IteratorAggregate: return internal iterator
     */
    public function getIterator(): PriorityList
    {
        return $this->iterator;
    }

    /**
     * Set the object used by the hydrator
     *
     * @param  object $object
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setObject($object)
    {
        if (! is_object($object)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object argument; received "%s"',
                __METHOD__,
                $object
            ));
        }

        $this->object = $object;
        return $this;
    }

    /**
     * Get the object used by the hydrator
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set the class or interface of objects that can be bound to this fieldset.
     *
     * @param null|class-string $allowObjectBindingClass
     */
    public function setAllowedObjectBindingClass(?string $allowObjectBindingClass): void
    {
        $this->allowedObjectBindingClass = $allowObjectBindingClass;
    }

    /**
     * Get The class or interface of objects that can be bound to this fieldset.
     *
     * @return null|class-string
     */
    public function allowedObjectBindingClass(): ?string
    {
        return $this->allowedObjectBindingClass;
    }

    /**
     * Checks if the object can be set in this fieldset
     *
     * @param object|array $object
     */
    public function allowObjectBinding($object): bool
    {
        $validBindingClass         = false;
        $allowedObjectBindingClass = $this->allowedObjectBindingClass();
        if (is_object($object) && $allowedObjectBindingClass !== null) {
            $objectClass       = ltrim($allowedObjectBindingClass, '\\');
            $reflection        = new ReflectionClass($object);
            $validBindingClass = $reflection->getName() === $objectClass
                || $reflection->isSubclassOf($allowedObjectBindingClass);
        }

        return $validBindingClass || ($this->object && $object instanceof $this->object);
    }

    /**
     * Set the hydrator to use when binding an object to the element
     *
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Set the hydrator by name to use when binding an object to the element.
     *
     * The form element manager {@see FormElementManager} is used via the
     * form factory {@see Factory} to fetch the hydrator.
     *
     * @throws Exception\DomainException If hydrator is not found in hydrator
     *                                   manager or service manager.
     */
    public function setHydratorByName(string $hydratorName): void
    {
        $this->setHydrator(
            $this->getFormFactory()
                ->getFormElementManager()
                ->getHydratorFromName($hydratorName)
        );
    }

    /**
     * Get the hydrator used when binding an object to the fieldset
     *
     * If no hydrator is present and object implements HydratorAwareInterface,
     * hydrator will be retrieved from the object.
     *
     * Will lazy-load Hydrator\ArraySerializable if none is present.
     */
    public function getHydrator(): HydratorInterface
    {
        if (! $this->hydrator instanceof HydratorInterface) {
            if ($this->object instanceof HydratorAwareInterface) {
                $hydrator = $this->object->getHydrator();
                assert($hydrator !== null);
                $this->setHydrator($hydrator);
            } else {
                $this->setHydrator(new Hydrator\ArraySerializableHydrator());
            }
            assert(null !== $this->hydrator);
        }
        return $this->hydrator;
    }

    /**
     * Checks if this fieldset can bind data
     */
    public function allowValueBinding(): bool
    {
        return is_object($this->object);
    }

    /**
     * Bind values to the bound object
     *
     * @param  array      $values
     * @param  array|null $validationGroup
     * @return mixed
     */
    public function bindValues(array $values = [], ?array $validationGroup = null)
    {
        $objectData     = $this->extract();
        $hydrator       = $this->getHydrator();
        $hydratableData = [];

        foreach ($this->iterator as $name => $element) {
            if (
                $validationGroup
                && (! array_key_exists($name, $validationGroup) && ! in_array($name, $validationGroup))
            ) {
                continue;
            }

            if (! array_key_exists($name, $values)) {
                if (! $element instanceof Collection) {
                    continue;
                }

                $values[$name] = [];
            }

            $value = $values[$name];

            if ($element instanceof FieldsetInterface && $element->allowValueBinding()) {
                $value = $element->bindValues($value, empty($validationGroup[$name]) ? null : $validationGroup[$name]);
            }

            // skip post values for disabled elements, get old value from object
            if (! $element->getAttribute('disabled')) {
                $hydratableData[$name] = $value;
            } elseif (array_key_exists($name, $objectData)) {
                $hydratableData[$name] = $objectData[$name];
            }
        }

        if (! empty($hydratableData) && $this->object) {
            $this->object = $hydrator->hydrate($hydratableData, $this->object);
        }

        return $this->object;
    }

    /**
     * Set if this fieldset is used as a base fieldset
     *
     * @return $this
     */
    public function setUseAsBaseFieldset(bool $useAsBaseFieldset)
    {
        $this->useAsBaseFieldset = $useAsBaseFieldset;
        return $this;
    }

    /**
     * Is this fieldset use as a base fieldset for a form ?
     */
    public function useAsBaseFieldset(): bool
    {
        return $this->useAsBaseFieldset;
    }

    /**
     * Extract values from the bound object
     *
     * @return array
     */
    protected function extract(): array
    {
        if (! is_object($this->object)) {
            return [];
        }

        $values = $this->getHydrator()->extract($this->object);

        // Recursively extract and populate values for nested fieldsets
        foreach ($this->fieldsets as $fieldset) {
            $name = (string) $fieldset->getName();

            if (isset($values[$name])) {
                $object = $values[$name];

                if ($fieldset instanceof Fieldset && $fieldset->allowObjectBinding($object)) {
                    $fieldset->setObject($object);
                    $values[$name] = $fieldset->extract();
                }
            }
        }

        return $values;
    }

    /**
     * Make a deep clone of a fieldset
     *
     * @return void
     */
    public function __clone()
    {
        $items = $this->iterator->toArray(PriorityList::EXTR_BOTH);

        $this->elements  = [];
        $this->fieldsets = [];
        $this->iterator  = new PriorityList();
        $this->iterator->isLIFO(false);

        foreach ($items as $name => $item) {
            $elementOrFieldset = clone $item['data'];

            $this->iterator->insert($name, $elementOrFieldset, $item['priority']);

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $this->fieldsets[$name] = $elementOrFieldset;
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $this->elements[$name] = $elementOrFieldset;
            }
        }
        $this->iterator->rewind();
        // Also make a deep copy of the object in case it's used within a collection
        if (is_object($this->object)) {
            $this->object = clone $this->object;
        }
    }
}
