<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\Form\Exception\ExceptionInterface;

interface ElementInterface
{
    /**
     * Set the name of this element
     *
     * In most cases, this will proxy to the attributes for storage, but is
     * present to indicate that elements are generally named.
     *
     * @return self
     */
    public function setName(string $name);

    /**
     * Retrieve the element name
     */
    public function getName(): ?string;

    /**
     * Set options for an element
     *
     * @return self
     */
    public function setOptions(iterable $options);

    /**
     * Set a single option for an element
     *
     * @return self
     */
    public function setOption(string $key, mixed $value);

    /**
     * get the defined options
     */
    public function getOptions(): array;

    /**
     * return the specified option
     *
     * @return null|mixed
     */
    public function getOption(string $option);

    /**
     * Set a single element attribute
     *
     * @param scalar|null $value
     * @return self
     */
    public function setAttribute(string $key, mixed $value);

    /**
     * Retrieve a single element attribute
     *
     * @return scalar|null
     */
    public function getAttribute(string $key);

    /**
     * Return true if a specific attribute is set
     */
    public function hasAttribute(string $key): bool;

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @param iterable<string, scalar|null> $arrayOrTraversable
     * @return self
     */
    public function setAttributes(iterable $arrayOrTraversable);

    /**
     * Retrieve all attributes at once
     *
     * @return array<string, scalar|null>
     */
    public function getAttributes(): array;

    /**
     * Set the value of the element
     *
     * @return self
     */
    public function setValue(mixed $value);

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set the label (if any) used for this element
     *
     * @return self
     */
    public function setLabel(?string $label);

    /**
     * Retrieve the label (if any) used for this element
     */
    public function getLabel(): ?string;

    /**
     * Set a list of messages to report when validation fails
     *
     * @return self
     */
    public function setMessages(iterable $messages);

    /**
     * Get validation error messages, if any
     *
     * Returns a list of validation failure messages, if any.
     *
     * @throws ExceptionInterface
     */
    public function getMessages(): array;
}
