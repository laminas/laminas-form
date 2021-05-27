<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class SampleEntity
{
    /**
     * @var null|string
     * @Annotation\ErrorMessage("Invalid or missing sampleinput")
     * @Annotation\Required(true)
     * @Annotation\AllowEmpty(true)
     * @Annotation\ContinueIfEmpty(true)
     */
    #[Annotation\ErrorMessage("Invalid or missing sampleinput")]
    #[Annotation\Required(true)]
    #[Annotation\AllowEmpty(true)]
    #[Annotation\ContinueIfEmpty(true)]
    public $sampleinput;

    /**
     * @var null|string
     * @Annotation\Attributes({"type":"text"})
     */
    #[Annotation\Attributes(["type" => "text"])]
    public $anotherSampleInput;
}
