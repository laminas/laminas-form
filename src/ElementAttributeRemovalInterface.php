<?php

declare(strict_types=1);

namespace Laminas\Form;

interface ElementAttributeRemovalInterface
{
    /**
     * Remove a single element attribute
     *
     * @return self
     */
    public function removeAttribute(string $key);

    /**
     * Remove many attributes at once
     *
     * @param list<string> $keys
     * @return self
     */
    public function removeAttributes(array $keys);

    /**
     * Remove all attributes at once
     *
     * @return self
     */
    public function clearAttributes();
}
