<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Psr\Container\ContainerInterface;

final class CustomElementWithConstructorDependencyFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array|null $options = null,
    ): CustomElementWithConstructorDependency {
        return new CustomElementWithConstructorDependency(
            null,
            $options ?? [],
            'FOO',
        );
    }
}
