<?php

declare(strict_types=1);

namespace Laminas\Form;

interface ElementAttributeRemovalInterface
{
    /**
     * Remove a single element attribute
     *
     * @return $this
     */
    public function removeAttribute(string $key);

    /**
     * Remove many attributes at once
     *
     * @param array $keys
     * @return $this
     */
    public function removeAttributes(array $keys);

    /**
     * Remove all attributes at once
     *
     * @return $this
     */
    public function clearAttributes();
}
