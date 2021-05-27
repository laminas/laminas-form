<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Entity;

class Address
{
    /** @var string */
    protected $street;

    /** @var City */
    protected $city;

    /** @var array */
    protected $phones = [];

    /**
     * @return $this
     */
    public function setStreet(string $street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return $this
     */
    public function setCity(City $city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param array $phones
     * @return $this
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }
}
