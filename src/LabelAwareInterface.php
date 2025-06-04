<?php

declare(strict_types=1);

namespace Laminas\Form;

interface LabelAwareInterface
{
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
     * Set the attributes to use with the label
     *
     * @param  array<string, scalar|null> $labelAttributes
     * @return self
     */
    public function setLabelAttributes(array $labelAttributes);

    /**
     * Get the attributes to use with the label
     *
     * @return array<string, scalar|null>
     */
    public function getLabelAttributes(): array;

    /**
     * Set many label options at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @return self
     */
    public function setLabelOptions(iterable $arrayOrTraversable);

    /**
     * Get label specific options
     *
     * @return array<string, mixed>
     */
    public function getLabelOptions(): array;

    /**
     * Set a single label optionn
     *
     * @param  mixed  $value
     * @return self
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
     * @return self
     */
    public function removeLabelOption(string $key);

    /**
     * Does the element has a specific label option ?
     */
    public function hasLabelOption(string $key): bool;

    /**
     * Remove many attributes at once
     *
     * @return self
     */
    public function removeLabelOptions(array $keys);

    /**
     * Clear all label options
     *
     * @return self
     */
    public function clearLabelOptions();
}
