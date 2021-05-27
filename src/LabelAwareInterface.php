<?php

declare(strict_types=1);

namespace Laminas\Form;

interface LabelAwareInterface
{
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
     * Set the attributes to use with the label
     *
     * @param  array $labelAttributes
     * @return $this
     */
    public function setLabelAttributes(array $labelAttributes);

    /**
     * Get the attributes to use with the label
     *
     * @return array
     */
    public function getLabelAttributes(): array;

    /**
     * Set many label options at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @return $this
     */
    public function setLabelOptions(iterable $arrayOrTraversable);

    /**
     * Get label specific options
     *
     * @return array
     */
    public function getLabelOptions(): array;

    /**
     * Set a single label optionn
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setLabelOption(string $key, $value);

    /**
     * Retrieve a single label option
     *
     * @param  int|string $key
     * @return mixed|null
     */
    public function getLabelOption($key);

    /**
     * Remove a single label option
     *
     * @return $this
     */
    public function removeLabelOption(string $key);

    /**
     * Does the element has a specific label option ?
     */
    public function hasLabelOption(string $key): bool;

    /**
     * Remove many attributes at once
     *
     * @param  array $keys
     * @return $this
     */
    public function removeLabelOptions(array $keys);

    /**
     * Clear all label options
     *
     * @return $this
     */
    public function clearLabelOptions();
}
