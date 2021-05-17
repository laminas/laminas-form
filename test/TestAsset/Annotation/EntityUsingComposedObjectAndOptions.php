<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 #[Annotation\Name("hierarchical")
 */
#[Annotation\Name("hierarchical")]
class EntityUsingComposedObjectAndOptions
{
    /**
     * @Annotation\Name("child")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject(targetObject="LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     */
    #[Annotation\Name("child")]
    #[Annotation\Options(["label" => "My label"])]
    #[Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection: true)]
    public $child;

    /**
     * @Annotation\Name("childTheSecond")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection=true)
     * @Annotation\Options({"label": "My label"})
     */
    #[Annotation\Name("childTheSecond")]
    #[Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity", isCollection: true)]
    #[Annotation\Options(["label" => "My label"])]
    public $childTheSecond;

    /**
     * @Annotation\Name("childTheThird")
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Options({"label": "My label"})
     */
    #[Annotation\Name("childTheThird")]
    #[Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")]
    #[Annotation\Options(["label" => "My label"])]
    public $childTheThird;

    /**
     * @Annotation\Name("childTheFourth")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")
     */
    #[Annotation\Name("childTheFourth")]
    #[Annotation\Options(["label" => "My label"])]
    #[Annotation\ComposedObject("LaminasTest\Form\TestAsset\Annotation\Entity")]
    public $childTheFourth;
}
