<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class SampleEntity
{
    /**
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

    /** @Annotation\Attributes({"type":"text"}) */
    #[Annotation\Attributes(["type" => "text"])]
    public $anotherSampleInput;
}
