<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Form\Annotation;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 */
#[Annotation\Hydrator(ObjectPropertyHydrator::class)]
class EntityObjectPropertyHydrator extends Entity
{
}
