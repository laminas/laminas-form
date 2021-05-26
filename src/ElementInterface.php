<?php

namespace Laminas\Form;

interface ElementInterface
{
    /**
     * Set the name of this element
     *
     * In most cases, this will proxy to the attributes for storage, but is
     * present to indicate that elements are generally named.
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Retrieve the element name
     *
     * @return string
     */
    public function getName();

    /**
     * Set options for an element
     *
     * @return $this
     */
    public function setOptions(iterable $options);

    /**
     * Set a single option for an element
     *
     * @param  string $key
     * @param  mixed $value
     * @return $this
     */
    public function setOption($key, $value);

    /**
     * get the defined options
     *
     * @return array
     */
    public function getOptions();

    /**
     * return the specified option
     *
     * @param string $option
     * @return null|mixed
     */
    public function getOption($option);

    /**
     * Set a single element attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return $this
     */
    public function setAttribute($key, $value);

    /**
     * Retrieve a single element attribute
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Return true if a specific attribute is set
     *
     * @param  string $key
     * @return bool
     */
    public function hasAttribute($key);

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
     * @param  null|string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Retrieve the label (if any) used for this element
     *
     * @return string
     */
    public function getLabel();

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
