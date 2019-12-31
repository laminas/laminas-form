<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Annotation;

use Laminas\Form\Exception\UnexpectedValueException;

/**
 * Object annotation
 *
 * Use this annotation to specify an object to use as the bound object of a form or fieldset
 *
 * @Annotation
 * @package    Laminas_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Laminas (https://www.zend.com)
 * @license    https://getlaminas.org/license/new-bsd     New BSD License
 */
class Object extends AbstractStringAnnotation
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
