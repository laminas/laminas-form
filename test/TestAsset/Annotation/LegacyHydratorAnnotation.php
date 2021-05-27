<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

// @codingStandardsIgnoreStart
/**
 * @Annotation\Name("user")
 * @Annotation\Hydrator({"type": "Laminas\Hydrator\ClassMethodsHydrator", "options":{"underscoreSeparatedKeys": false}})
 */
#[Annotation\Name("user")]
#[Annotation\Hydrator(["type" => "Laminas\Hydrator\ClassMethodsHydrator", "options" => ["underscoreSeparatedKeys" => false]])]
class LegacyHydratorAnnotation
{
    /**
     * @Annotation\Options({"label":"Username:", "label_attributes": {"class": "label"}})
     */
    #[Annotation\Options(["label" => "Username:", "label_attributes" => ["class" => "label"]])]
    public $username;
}
// @codingStandardsIgnoreEnd
