<?php

declare(strict_types=1);

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
     * @var null|Entity
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     */
    #[Annotation\Name("composed")]
    #[Annotation\ComposedObject(Entity::class, isCollection: true)]
    public $child;
}
