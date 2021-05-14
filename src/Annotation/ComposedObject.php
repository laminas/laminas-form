<?php

namespace Laminas\Form\Annotation;

use function is_array;

/**
 * ComposedObject annotation
 *
 * Use this annotation to specify another object with annotations to parse
 * which you can then add to the form as a fieldset. The value should be a
 * string indicating the fully qualified class name of the composed object
 * to use.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
class ComposedObject
{
    /**
     * @var string|null
     */
    protected $targetObject;

    /**
     * @var bool
     */
    protected $isCollection;

    /**
     * @var array
     */
    protected $options;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array|string $targetObject
     * @param bool $isCollection
     * @param array $options
     */
    public function __construct($targetObject, bool $isCollection = false, array $options = [])
    {
        // support for legacy notation
        if (is_array($targetObject)) {
            $this->targetObject = $targetObject['target_object'] ?? null;
            $this->isCollection = $targetObject['is_collection'] ?? false;
            $this->options = $targetObject['options'] ?? [];
        } else {
            $this->targetObject = $targetObject;
            $this->isCollection = $isCollection;
            $this->options = $options;
        }
    }

    /**
     * Retrieve the composed object classname
     *
     * @return null|string
     */
    public function getComposedObject(): ?string
    {
        return $this->targetObject;
    }

    /**
     * Is this composed object a collection or not
     *
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->isCollection;
    }

    /**
     * Retrieve the options for the composed object
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
