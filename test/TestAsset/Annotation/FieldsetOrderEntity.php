<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class FieldsetOrderEntity
{
    /**
     * @Annotation\Type("Laminas\Form\Fieldset")
     */
    public $fieldset;

    /**
     * @Annotation\Type("Laminas\Form\Element")
     */
    public $element;
}
