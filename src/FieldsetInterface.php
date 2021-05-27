<?php

declare(strict_types=1);

namespace Laminas\Form;

use Countable;
use IteratorAggregate;
use Laminas\Hydrator\HydratorInterface;
use Traversable;

interface FieldsetInterface extends
    Countable,
    IteratorAggregate,
    ElementInterface,
    ElementPrepareAwareInterface,
    FormFactoryAwareInterface
{
    /**
     * Add an element or fieldset
     *
     * $flags could contain metadata such as the alias under which to register
     * the element or fieldset, order in which to prioritize it, etc.
     *
     * @param  array|Traversable|ElementInterface $elementOrFieldset Typically, only allow objects implementing
     *                                                               ElementInterface; however, keeping it flexible
     *                                                               to allow a factory-based form
     *                                                               implementation as well
     * @param  array $flags
     * @return $this
     */
    public function add($elementOrFieldset, array $flags = []);

    /**
     * Does the fieldset have an element/fieldset by the given name?
     */
    public function has(string $elementOrFieldset): bool;

    /**
     * Retrieve a named element or fieldset
     */
    public function get(string $elementOrFieldset): ElementInterface;

    /**
     * Remove a named element or fieldset
     *
     * @return $this
     */
    public function remove(string $elementOrFieldset);

    /**
     * Set/change the priority of an element or fieldset
     *
     * @return $this
     */
    public function setPriority(string $elementOrFieldset, int $priority);

    /**
     * Retrieve all attached elements
     *
     * Storage is an implementation detail of the concrete class.
     *
     * @return ElementInterface[]
     */
    public function getElements(): array;

    /**
     * Retrieve all attached fieldsets
     *
     * Storage is an implementation detail of the concrete class.
     *
     * @return FieldsetInterface[]
     */
    public function getFieldsets(): array;

    /**
     * Recursively populate value attributes of elements
     */
    public function populateValues(iterable $data): void;

    /**
     * Set the object used by the hydrator
     *
     * @param  mixed $object
     * @return $this
     */
    public function setObject($object);

    /**
     * Get the object used by the hydrator
     *
     * @return mixed
     */
    public function getObject();

    /**
     * Checks if the object can be set in this fieldset
     */
    public function allowObjectBinding(object $object): bool;

    /**
     * Set the hydrator to use when binding an object to the element
     *
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator);

    /**
     * Get the hydrator used when binding an object to the element
     */
    public function getHydrator(): ?HydratorInterface;

    /**
     * Bind values to the bound object
     *
     * @return mixed
     */
    public function bindValues(array $values = []);

    /**
     * Checks if this fieldset can bind data
     */
    public function allowValueBinding(): bool;
}
