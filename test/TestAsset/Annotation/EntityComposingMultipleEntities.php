<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Name("hierarchical")
 */
#[Annotation\Name("hierarchical")]
class EntityComposingMultipleEntities
{
    /**
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject({"target_object":"LaminasTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     */
    #[Annotation\Name("composed")]
    #[Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", is_collection: "true")]
    public $child;
}
