<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Exclude annotation
 *
 * Presence of this annotation hints to the AnnotationBuilder to skip the
 * element when creating the form specification.
 *
 * @Annotation
 * @NamedArgumentConstructor
 */
#[Attribute]
final class Exclude
{
}
