<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
 * @Annotation\Options({"use_as_base_fieldset":true})
 */
#[Annotation\Options(["use_as_base_fieldset" => true])]
class EntityUsingInstanceProperty
{
    /**
     * @Annotation\Instance("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Type("Laminas\Form\Fieldset")
     * @Annotation\Hydrator({
     *     "type":"Laminas\Hydrator\ClassMethodsHydrator",
     *     "options": {"underscoreSeparatedKeys": false}
     * })
     */
    #[Annotation\Instance("LaminasTest\Form\TestAsset\Annotation\Entity")]
    #[Annotation\Type("Laminas\Form\Fieldset")]
    // @codingStandardsIgnoreLine
    #[Annotation\Hydrator(["type" => "Laminas\Hydrator\ClassMethodsHydrator", "options" => ["underscoreSeparatedKeys" => false]])]
    public $object;
}
