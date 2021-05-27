<?php

declare(strict_types=1);

namespace Laminas\Form;

interface ElementInterface
{
    /**
     * Set the name of this element
     *
     * In most cases, this will proxy to the attributes for storage, but is
     * present to indicate that elements are generally named.
     *
     * @return $this
     */
    public function setName(string $name);

    /**
     * Retrieve the element name
     */
    public function getName(): ?string;

    /**
     * Set options for an element
     *
     * @return $this
     */
    public function setOptions(iterable $options);

    /**
     * Set a single option for an element
     *
     * @param  mixed $value
     * @return $this
     */
    public function setOption(string $key, $value);

    /**
     * get the defined options
     *
     * @return array
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
     * @param  mixed $value
     * @return $this
     */
    public function setAttribute(string $key, $value);

    /**
     * Retrieve a single element attribute
     *
     * @return mixed
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
     * @return $this
     */
    public function setAttributes(iterable $arrayOrTraversable);

    /**
     * Retrieve all attributes at once
     */
    public function getAttributes(): array;

    /**
     * Set the value of the element
     *
     * @param  mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set the label (if any) used for this element
     *
     * @return $this
     */
    public function setLabel(?string $label);

    /**
     * Retrieve the label (if any) used for this element
     */
    public function getLabel(): ?string;

    /**
     * Set a list of messages to report when validation fails
     *
     * @return $this
     */
    public function setMessages(iterable $messages);

    /**
     * Get validation error messages, if any
     *
     * Returns a list of validation failure messages, if any.
     */
    public function getMessages(): array;
}
