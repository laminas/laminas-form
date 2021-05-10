<?php

namespace LaminasTest\Form\TestAsset;

use DateTime;
use Laminas\Form\Form;

class CustomCreatedForm extends Form
{
    private $created;

    public function __construct(DateTime $created, $name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
