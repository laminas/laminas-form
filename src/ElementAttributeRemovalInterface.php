<?php

namespace Laminas\Form;

interface ElementAttributeRemovalInterface
{
    /**
     * Remove a single element attribute
     *
     * @param  string $key
     * @return $this
     */
    public function removeAttribute($key);

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
