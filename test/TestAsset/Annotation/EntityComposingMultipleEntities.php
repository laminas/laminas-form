<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\Entity;

/**
 * @Annotation\Name("hierarchical")
 */
#[Annotation\Name("hierarchical")]
class EntityComposingMultipleEntities
{
    /**
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     */
    #[Annotation\Name("composed")]
    #[Annotation\ComposedObject(Entity::class, isCollection: true)]
    public $child;
}
