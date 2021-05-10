<?php

namespace LaminasTest\Form\TestAsset\Entity;

class Phone
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
