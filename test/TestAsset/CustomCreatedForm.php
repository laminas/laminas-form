<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use DateTime;
use Laminas\Form\Form;

class CustomCreatedForm extends Form
{
    private $created;

    public function __construct(DateTime $created, $name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
