<?php

declare(strict_types=1);

namespace Laminas\Form\Annotation;

use ArrayObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionException;

/**
 * Parses the properties of a class for annotations in order to create a form
 * and input filter definition.
 */
final class AnnotationBuilder extends AbstractBuilder
{
    /**
     * Initialize the annotation registry
     */
    public function __construct()
    {
        // doctrine/annotations 1.x does not require autoloading by default
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * Derive a form specification from doctrine annotations for a given entity
     *
     * @param  object|class-string $entity
     * @throws ReflectionException
     * @return array
     */
    protected function getFormSpecificationInternal($entity): array
    {
        $formSpec   = new ArrayObject();
        $filterSpec = new ArrayObject();

        $reflection = new ReflectionClass($entity);
        $reader     = new AnnotationReader();

        $annotations = new AnnotationCollection($reader->getClassAnnotations($reflection));
        $this->configureForm($annotations, $reflection, $formSpec, $filterSpec);

        foreach ($reflection->getProperties() as $property) {
            $annotations = new AnnotationCollection($reader->getPropertyAnnotations($property));
            $this->configureElement($annotations, $property, $formSpec, $filterSpec);
        }

        return [$formSpec, $filterSpec];
    }
}
