<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

class LegacyFilterAnnotation
{
    /**
     * @var null|string
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim"})
     */
    #[Annotation\Required(true)]
    #[Annotation\Filter(["name" => "StringTrim"])]
    public $username;
}
