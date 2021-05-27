<?php

declare(strict_types=1);

namespace Laminas\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

use function array_pop;
use function explode;
use function strtolower;

/**
 * Factory for instantiating form elements
 */
final class ElementFactory implements FactoryInterface
{
    /**
     * Create an instance of the requested class name.
     *
     * @param string $requestedName
     * @param null|array $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): object
    {
        if ($options === null) {
            $options = [];
        }

        if (isset($options['name'])) {
            $name = $options['name'];
        } else {
            // 'Laminas\Form\Element' -> 'element'
            $parts = explode('\\', $requestedName);
            $name  = strtolower(array_pop($parts));
        }

        if (isset($options['options'])) {
            $options = $options['options'];
        }

        return new $requestedName($name, $options);
    }
}
