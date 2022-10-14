<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use DateTime;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CustomCreatedFormFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, ?array $options = null)
    {
        $options        = $options ?: [];
        $creationString = 'now';

        if (isset($options['created'])) {
            $creationString = $options['created'];
            unset($options['created']);
        }

        $created = new DateTime($creationString);

        $name = null;
        if (isset($options['name'])) {
            $name = $options['name'];
            unset($options['name']);
        }

        return new CustomCreatedForm($created, $name, $options);
    }
}
