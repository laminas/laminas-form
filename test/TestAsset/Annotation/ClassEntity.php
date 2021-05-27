<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use LaminasTest\Form\TestAsset\Annotation\InputFilter;

/**
 * @Annotation\Name("some_name")
 * @Annotation\Attributes({"legend":"Some Fieldset"})
 * @Annotation\InputFilter("LaminasTest\Form\TestAsset\Annotation\InputFilter")
 * @Annotation\ValidationGroup({"omit", "keep"})
 */
#[Annotation\Name("some_name")]
#[Annotation\Attributes(["legend" => "Some Fieldset"])]
#[Annotation\InputFilter(InputFilter::class)]
#[Annotation\ValidationGroup(["omit", "keep"])]
class ClassEntity
{
    /**
     * @var null|string
     * @Annotation\Exclude()
     */
    #[Annotation\Exclude]
    public $omit;

    /**
     * @var null|string
     * @Annotation\Name("keeper")
     * @Annotation\Attributes({"type":"text"})
     */
    #[Annotation\Name("keeper")]
    #[Annotation\Attributes(["type" => "text"])]
    public $keep;
}
