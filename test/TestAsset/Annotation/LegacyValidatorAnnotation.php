<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class LegacyValidatorAnnotation
{
    /**
     * @Annotation\Required(true)
     * @Annotation\Validator({"name": "StringLength", "options":{"min":3,"max":25}})
     */
    #[Annotation\Required(true)]
    #[Annotation\Validator(["name" => "StringLength", "options" => ["min" => 3, "max" => 25]])]
    public $username;
}
