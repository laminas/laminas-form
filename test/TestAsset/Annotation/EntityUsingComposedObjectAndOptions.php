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
class EntityUsingComposedObjectAndOptions
{
    /**
     * @Annotation\Name("child")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject({"target_object":"LaminasTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     */
    public $child;

    /**
     * @Annotation\Name("childTheSecond")
     * @Annotation\ComposedObject({"target_object":"LaminasTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     * @Annotation\Options({"label": "My label"})
     */
    public $childTheSecond;

    /**
     * @Annotation\Name("childTheThird")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Options({"label": "My label"})
     */
    public $childTheThird;

    /**
     * @Annotation\Name("childTheFourth")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     */
    public $childTheFourth;
}
