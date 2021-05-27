<?php

namespace LaminasTest\Form\TestAsset\Entity;

class Country
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $continent;

    /**
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setContinent(string $continent)
    {
        $this->continent = $continent;
        return $this;
    }

    /**
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }
}
