<?php

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

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
class Instance
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

    /**
     * Retrieve the instance
     *
     * @deprecated 3.0.0 Use getInstance() instead
     */
    public function getObject(): string
    {
        trigger_error(sprintf(
            'Calling %s::%s is deprecated since 3.0.0, use getInstance() instead.',
            static::class,
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->getInstance();
    }
}
