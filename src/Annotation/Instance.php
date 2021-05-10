<?php

namespace Laminas\Form\Annotation;

/**
 * Instance (formerly "object") annotation
 *
 * Use this annotation to specify an object instance to use as the bound object
 * of a form or fieldset
 *
 * @Annotation
 * @copyright  Copyright (c) 2005-2015 Laminas (https://www.zend.com)
 * @license    https://getlaminas.org/license/new-bsd     New BSD License
 */
class Instance extends AbstractStringAnnotation
{
    /**
     * Retrieve the object
     *
     * @return null|string
     */
    public function getObject()
    {
        return $this->value;
    }
}
