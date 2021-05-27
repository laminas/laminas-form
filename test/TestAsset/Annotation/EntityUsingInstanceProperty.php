<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethodsHydrator;
use LaminasTest\Form\TestAsset\Annotation\Entity;

/**
 * @Annotation\Options({"use_as_base_fieldset":true})
 */
#[Annotation\Options(["use_as_base_fieldset" => true])]
class EntityUsingInstanceProperty
{
    /**
     * @var null|Entity
     * @Annotation\Instance("LaminasTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Type("Laminas\Form\Fieldset")
     * @Annotation\Hydrator(
     *     "Laminas\Hydrator\ClassMethodsHydrator",
     *     options={"underscoreSeparatedKeys": false}
     * )
     */
    #[Annotation\Instance(Entity::class)]
    #[Annotation\Type(Fieldset::class)]
    #[Annotation\Hydrator(ClassMethodsHydrator::class, options: ["underscoreSeparatedKeys" => false])]
    public $object;
}
