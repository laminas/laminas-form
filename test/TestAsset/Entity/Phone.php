<?php

namespace LaminasTest\Form\TestAsset\Entity;

class Phone
{
    /** @var string */
    protected $number;

    /**
     * @return $this
     */
    public function setNumber(string $number)
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
