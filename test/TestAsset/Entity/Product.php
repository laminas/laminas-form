<?php

namespace LaminasTest\Form\TestAsset\Entity;

use function get_object_vars;

class Product
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $price;

    /** @var Category[] */
    protected $categories;

    /** @var Country */
    protected $madeInCountry;

    /**
     * @param Category[] $categories
     * @return $this
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

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
    public function setPrice(int $price)
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
     */
    public function getCategory(int $i): Category
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

    public function setMadeInCountry(Country $country): void
    {
        $this->madeInCountry = $country;
    }
}
