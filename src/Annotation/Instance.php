<?php

namespace Laminas\Form\Annotation;

/**
 * Instance (formerly "object") annotation
 *
 * Use this annotation to specify an object instance to use as the bound object
 * of a form or fieldset
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @copyright  Copyright (c) 2005-2015 Laminas (https://www.zend.com)
 * @license    https://getlaminas.org/license/new-bsd     New BSD License
 */
class Instance
{
    /**
     * @var string
     */
    protected $instance;

    /**
     * Receive and process the contents of an annotation
     *
     * @param string $instance
     */
    public function __construct(string $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Retrieve the instance
     *
     * @return string
     */
    public function getInstance(): string
    {
        return $this->instance;
    }

    /**
     * Retrieve the instance
     *
     * @return string
     * @deprecated 3.0.0 Use getInstance() instead
     */
    public function getObject(): string
    {
        return $this->getInstance();
    }
}
