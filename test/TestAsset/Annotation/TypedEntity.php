<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Type("LaminasTest\Form\TestAsset\Annotation\Form")
 */
class TypedEntity
{
    /**
     * @Annotation\Type("LaminasTest\Form\TestAsset\Annotation\Element")
     * @Annotation\Name("typed_element")
     */
    public $typedElement;
}
