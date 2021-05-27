<?php

declare(strict_types=1);

namespace Laminas\Form;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\InitializableInterface;
use Traversable;

use function array_key_exists;
use function is_string;

class Element implements
    ElementAttributeRemovalInterface,
    ElementInterface,
    InitializableInterface,
    LabelAwareInterface
{
    /** @var array  */
    protected $attributes = [];

    /** @var null|string */
    protected $label;

    /** @var array */
    protected $labelAttributes = [];

    /**
     * Label specific options
     *
     * @var array
     */
    protected $labelOptions = [];

    /** @var array Validation error messages */
    protected $messages = [];

    /** @var array custom options */
    protected $options = [];

    /** @var mixed */
    protected $value;

    /** @var boolean */
    protected $hasValue = false;

    /**
     * @param  null|int|string   $name    Optional name for the element
     * @param  iterable $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name = null, iterable $options = [])
    {
        if (null !== $name) {
            $this->setName((string) $name);
        }

        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * This function is automatically called when creating element with factory. It
     * allows to perform various operations (add elements...)
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set value for name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->setAttribute('name', $name);
        return $this;
    }

    /**
     * Get value for name
     */
    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - label_options: label specific options
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions(iterable $options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['label'])) {
            $this->setLabel($options['label']);
        }

        if (isset($options['label_attributes'])) {
            $this->setLabelAttributes($options['label_attributes']);
        }

        if (isset($options['label_options'])) {
            $this->setLabelOptions($options['label_options']);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get defined options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Return the specified option
     *
     * @return null|mixed
     */
    public function getOption(string $option)
    {
        if (! isset($this->options[$option])) {
            return null;
        }

        return $this->options[$option];
    }

    /**
     * Set a single option for an element
     *
     * @param  mixed $value
     * @return $this
     */
    public function setOption(string $key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Set a single element attribute
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute(string $key, $value)
    {
        // Do not include the value in the list of attributes
        if ($key === 'value') {
            $this->setValue($value);
            return $this;
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single element attribute
     *
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        if (! isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * Remove a single attribute
     *
     * @return $this
     */
    public function removeAttribute(string $key)
    {
        unset($this->attributes[$key]);
        return $this;
    }

    /**
     * Does the element has a specific attribute ?
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setAttributes(iterable $arrayOrTraversable)
    {
        foreach ($arrayOrTraversable as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * Retrieve all attributes at once
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Remove many attributes at once
     *
     * @param array $keys
     * @return $this
     */
    public function removeAttributes(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * Clear all attributes
     *
     * @return $this
     */
    public function clearAttributes()
    {
        $this->attributes = [];
        return $this;
    }

    /**
     * Set the element value
     *
     * @param  mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value    = $value;
        $this->hasValue = true;

        return $this;
    }

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the label used for this element
     *
     * @return $this
     */
    public function setLabel(?string $label)
    {
        if (is_string($label)) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * Retrieve the label used for this element
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the attributes to use with the label
     *
     * @param array $labelAttributes
     * @return $this
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the attributes to use with the label
     *
     * @return array
     */
    public function getLabelAttributes(): array
    {
        return $this->labelAttributes;
    }

    /**
     * Set many label options at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setLabelOptions(iterable $arrayOrTraversable)
    {
        foreach ($arrayOrTraversable as $key => $value) {
            $this->setLabelOption($key, $value);
        }
        return $this;
    }

    /**
     * Get label specific options
     *
     * @return array
     */
    public function getLabelOptions(): array
    {
        return $this->labelOptions;
    }

    /**
     * Clear all label options
     *
     * @return $this
     */
    public function clearLabelOptions()
    {
        $this->labelOptions = [];
        return $this;
    }

    /**
     * Remove many attributes at once
     *
     * @param array $keys
     * @return $this
     */
    public function removeLabelOptions(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->labelOptions[$key]);
        }

        return $this;
    }

    /**
     * Set a single label optionn
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setLabelOption(string $key, $value)
    {
        $this->labelOptions[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single label option
     *
     * @param  int|string $key
     * @return mixed|null
     */
    public function getLabelOption($key)
    {
        if (! isset($this->labelOptions[$key])) {
            return null;
        }

        return $this->labelOptions[$key];
    }

    /**
     * Remove a single label option
     *
     * @return $this
     */
    public function removeLabelOption(string $key)
    {
        unset($this->labelOptions[$key]);
        return $this;
    }

    /**
     * Does the element has a specific label option ?
     */
    public function hasLabelOption(string $key): bool
    {
        return array_key_exists($key, $this->labelOptions);
    }

    /**
     * Set a list of messages to report when validation fails
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages(iterable $messages)
    {
        if ($messages instanceof Traversable) {
            $messages = ArrayUtils::iteratorToArray($messages);
        }
        $this->messages = $messages;
        return $this;
    }

    /**
     * Get validation error messages, if any.
     *
     * Returns a list of validation failure messages, if any.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasValue(): bool
    {
        return $this->hasValue;
    }
}
