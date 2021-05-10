<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;

/**
* @Annotation\Options({"use_as_base_fieldset":true})
*/
class EntityUsingInstancePropertyHydratorV2
{
    /**
     * @Annotation\Instance("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Type("Laminas\Form\Fieldset")
     * @Annotation\Hydrator({"type":"Laminas\Hydrator\ClassMethods", "options": {"underscoreSeparatedKeys": false}})
     */
    public $object;
}
