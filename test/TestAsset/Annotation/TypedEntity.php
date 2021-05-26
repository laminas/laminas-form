<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\Element;
use LaminasTest\Form\TestAsset\Annotation\Form;

/**
 * @Annotation\Type("LaminasTest\Form\TestAsset\Annotation\Form")
 */
#[Annotation\Type(Form::class)]
class TypedEntity
{
    /**
     * @var null|Element
     * @Annotation\Type("LaminasTest\Form\TestAsset\Annotation\Element")
     * @Annotation\Name("typed_element")
     */
    #[Annotation\Type(Element::class)]
    #[Annotation\Name("typed_element")]
    public $typedElement;
}
