<?php

declare(strict_types=1);

namespace LaminasTest\Form\Annotation;

use Laminas\Form\Annotation;

final class AnnotationBuilderTest extends AbstractBuilderTestCase
{
    protected function createBuilder(): Annotation\AbstractBuilder
    {
        return new Annotation\AnnotationBuilder();
    }
}
