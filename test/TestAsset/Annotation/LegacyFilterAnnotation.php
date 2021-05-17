<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class LegacyFilterAnnotation
{
    /**
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim"})
     */
    #[Annotation\Required(true)]
    #[Annotation\Filter(["name" => "StringTrim"])]
    public $username;
}
