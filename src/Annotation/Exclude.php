<?php

namespace Laminas\Form\Annotation;

use Attribute;

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
class Exclude
{
}
