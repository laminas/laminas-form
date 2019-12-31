<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Annotation;

/**
 * ComposedObject annotation
 *
 * Use this annotation to specify another object with annotations to parse
 * which you can then add to the form as a fieldset. The value should be a
 * string indicating the fully qualified class name of the composed object
 * to use.
 *
 * @Annotation
 * @package    Laminas_Form
 * @subpackage Annotation
 */
class ComposedObject extends AbstractStringAnnotation
{
    /**
     * Retrieve the composed object classname
     *
     * @return null|string
     */
    public function getComposedObject()
    {
        return $this->value;
    }
}
