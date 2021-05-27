<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * @Annotation\Name("user")
 * @Annotation\Attributes({"legend":"Register"})
 * @Annotation\Hydrator("Laminas\Hydrator\ClassMethodsHydrator", options={"underscoreSeparatedKeys": false})
 */
#[Annotation\Name("user")]
#[Annotation\Attributes(["legend" => "Register"])]
#[Annotation\Hydrator(ClassMethodsHydrator::class, options: ["underscoreSeparatedKeys" => false])]
class EntityWithHydratorArray
{
    /**
     * @var null|string
     * @Annotation\Options({"label":"Username:", "label_attributes": {"class": "label"}})
     */
    #[Annotation\Options(["label" => "Username:", "label_attributes" => ["class" => "label"]])]
    public $username;
}
