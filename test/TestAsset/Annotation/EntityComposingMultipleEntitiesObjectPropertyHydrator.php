<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * @Annotation\Name("hierarchical")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 */
#[Annotation\Name("hierarchical")]
#[Annotation\Hydrator(ObjectPropertyHydrator::class)]
class EntityComposingMultipleEntitiesObjectPropertyHydrator
{
    /**
     * @var null|Entity
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\EntityObjectPropertyHydrator", isCollection=true)
     */
    #[Annotation\ComposedObject(
        EntityObjectPropertyHydrator::class,
        isCollection: true,
        options: ['create_new_objects' => true]
    )]
    public $child;
}
