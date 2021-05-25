<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class FieldsetOrderEntity
{
    /** @Annotation\Type("Laminas\Form\Fieldset") */
    #[Annotation\Type(Fieldset::class)]
    public $fieldset;

    /** @Annotation\Type("Laminas\Form\Element") */
    #[Annotation\Type(Element::class)]
    public $element;
}
