<?php

namespace LaminasTest\Form\TestAsset;

use DateTime;
use Laminas\Form\Form;

class CustomCreatedForm extends Form
{
    /** @var DateTime */
    private $created;

    public function __construct(DateTime $created, ?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
        $this->created = $created;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
