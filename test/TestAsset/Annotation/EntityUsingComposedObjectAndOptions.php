<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\Entity;

/**
 #[Annotation\Name("hierarchical")
 */
#[Annotation\Name("hierarchical")]
class EntityUsingComposedObjectAndOptions
{
    /**
     * @var null|Entity
     * @Annotation\Name("child")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject(targetObject="LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     */
    #[Annotation\Name("child")]
    #[Annotation\Options(["label" => "My label"])]
    #[Annotation\ComposedObject(Entity::class, isCollection: true)]
    public $child;

    /**
     * @var null|Entity
     * @Annotation\Name("childTheSecond")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     * @Annotation\Options({"label": "My label"})
     */
    #[Annotation\Name("childTheSecond")]
    #[Annotation\ComposedObject(Entity::class, isCollection: true)]
    #[Annotation\Options(["label" => "My label"])]
    public $childTheSecond;

    /**
     * @var null|Entity
     * @Annotation\Name("childTheThird")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Options({"label": "My label"})
     */
    #[Annotation\Name("childTheThird")]
    #[Annotation\ComposedObject(Entity::class)]
    #[Annotation\Options(["label" => "My label"])]
    public $childTheThird;

    /**
     * @var null|Entity
     * @Annotation\Name("childTheFourth")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     */
    #[Annotation\Name("childTheFourth")]
    #[Annotation\Options(["label" => "My label"])]
    #[Annotation\ComposedObject(Entity::class)]
    public $childTheFourth;
}
