<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset\Entity;

use function get_object_vars;

class Orphan
{
    /**
     * Name
     * @var string
     */
    public $name;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data = [])
    {
        $this->name = $data['name'];
    }
}
