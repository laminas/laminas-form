<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class FieldsetOrderEntity
{
    /**
     * @var null|Fieldset
     * @Annotation\Type("Laminas\Form\Fieldset")
     */
    #[Annotation\Type(Fieldset::class)]
    public $fieldset;

    /**
     * @var null|Element
     * @Annotation\Type("Laminas\Form\Element")
     */
    #[Annotation\Type(Element::class)]
    public $element;
}
