<?php

declare(strict_types=1);

namespace Laminas\Form;

use function array_key_exists;

trait LabelAwareTrait
{
    /**
     * Label specific html attributes
     *
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * Label specific options
     *
     * @var array
     */
    protected $labelOptions = [];

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
     * @param  array $keys
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
     * @return mixed|null
     */
    public function getLabelOption(string $key)
    {
        if (! array_key_exists($key, $this->labelOptions)) {
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
}
