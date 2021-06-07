<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Instance (formerly "object") annotation
 *
 * Use this annotation to specify an object instance to use as the bound object
 * of a form or fieldset
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Instance
{
    /** @var string */
    protected $instance;

    /**
     * Receive and process the contents of an annotation
     */
    public function __construct(string $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Retrieve the instance
     */
    public function getInstance(): string
    {
        return $this->instance;
    }
}
