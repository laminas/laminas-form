<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Name("hierarchical")
 */
class EntityComposingMultipleEntities
{
    /**
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject({"target_object":"LaminasTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     */
    public $child;
}
