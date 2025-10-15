<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use DateTime;
use Laminas\Form\Form;

/** @extends Form<array<string, mixed>> */
final class CustomCreatedForm extends Form
{
    public function __construct(private readonly DateTime $created, ?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
