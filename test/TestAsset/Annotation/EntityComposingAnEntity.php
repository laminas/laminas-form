<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\Entity;

/**
 * @Annotation\Name("hierarchical")
 */
#[Annotation\Name("hierarchical")]
class EntityComposingAnEntity
{
    /**
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     */
    #[Annotation\Name("composed")]
    #[Annotation\ComposedObject(Entity::class)]
    public $child;
}
