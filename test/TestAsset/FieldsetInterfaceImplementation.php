<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\Form\Factory;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Hydrator\HydratorInterface;
use Traversable;

final class FieldsetInterfaceImplementation implements FieldsetInterface
{
    private Factory $formFactory;

    public function __construct()
    {
        $this->formFactory = new Factory();
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator([]);
    }

    public function count(): int
    {
        return 0;
    }

    public function setName(string $name)
    {
        return $this;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function setOptions(iterable $options)
    {
        return $this;
    }

    public function setOption(string $key, mixed $value)
    {
        return $this;
    }

    public function getOptions(): array
    {
        return [];
    }

    public function getOption(string $option)
    {
        return null;
    }

    public function setAttribute(string $key, mixed $value)
    {
        return $this;
    }

    public function getAttribute(string $key)
    {
        return null;
    }

    public function hasAttribute(string $key): bool
    {
        return false;
    }

    public function setAttributes(iterable $arrayOrTraversable)
    {
        return $this;
    }

    public function getAttributes(): array
    {
        return [];
    }

    public function setValue(mixed $value)
    {
        return $this;
    }

    public function getValue()
    {
        return null;
    }

    public function setLabel(?string $label)
    {
        return $this;
    }

    public function getLabel(): ?string
    {
        return null;
    }

    public function setMessages(iterable $messages)
    {
        return $this;
    }

    public function getMessages(): array
    {
        return [];
    }

    public function prepareElement(FormInterface $form): void
    {
    }

    /**
     * @inheritDoc
     */
    public function add($elementOrFieldset, array $flags = [])
    {
        return $this;
    }

    public function has(string $elementOrFieldset): bool
    {
        return false;
    }

    public function get(string $elementOrFieldset): ElementInterface
    {
        return new Element();
    }

    public function remove(string $elementOrFieldset)
    {
        return $this;
    }

    public function setPriority(string $elementOrFieldset, int $priority)
    {
        return $this;
    }

    public function getElements(): array
    {
        return [];
    }

    public function getFieldsets(): array
    {
        return [];
    }

    public function populateValues(iterable $data): void
    {
    }

    /**
     * @inheritDoc
     */
    public function setObject($object)
    {
        return $this;
    }

    public function getObject()
    {
        return null;
    }

    public function allowObjectBinding(object $object): bool
    {
        return false;
    }

    public function setHydrator(HydratorInterface $hydrator)
    {
        return $this;
    }

    public function getHydrator(): ?HydratorInterface
    {
        return null;
    }

    public function bindValues(array $values = [])
    {
        return null;
    }

    public function allowValueBinding(): bool
    {
        return false;
    }

    public function setFormFactory(Factory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    public function getFormFactory(): Factory
    {
        return $this->formFactory;
    }
}
