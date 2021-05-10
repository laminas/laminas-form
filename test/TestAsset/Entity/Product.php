<?php

namespace LaminasTest\Form\TestAsset\Entity;

use function get_object_vars;

class Product
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var array
     */
    protected $categories;

    /**
     * @var Country
     */
    protected $madeInCountry;

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
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
     * @param int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Return category from index
     *
     * @param int $i
     */
    public function getCategory($i)
    {
        return $this->categories[$i];
    }

    /**
     * Required when binding to a form
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return Country
     */
    public function getMadeInCountry()
    {
        return $this->madeInCountry;
    }

    /**
     * @param Country $country
     */
    public function setMadeInCountry($country)
    {
        $this->madeInCountry = $country;
    }
}
