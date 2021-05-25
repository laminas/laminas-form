<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Name("some_name")
 * @Annotation\Attributes({"legend":"Some Fieldset"})
 * @Annotation\InputFilter("LaminasTest\Form\TestAsset\Annotation\InputFilter")
 * @Annotation\ValidationGroup({"omit", "keep"})
 */
#[Annotation\Name("some_name")]
#[Annotation\Attributes(["legend" => "Some Fieldset"])]
#[Annotation\InputFilter("LaminasTest\Form\TestAsset\Annotation\InputFilter")]
#[Annotation\ValidationGroup(["omit", "keep"])]
class ClassEntity
{
    /**
     * @Annotation\Exclude()
     */
    #[Annotation\Exclude]
    public $omit;

    /**
     * @Annotation\Name("keeper")
     * @Annotation\Attributes({"type":"text"})
     */
    #[Annotation\Name("keeper")]
    #[Annotation\Attributes(["type" => "text"])]
    public $keep;
}
