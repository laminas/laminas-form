<?php

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
