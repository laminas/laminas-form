<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset\Entity;

use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

class Cat implements InputFilterAwareInterface
{
    /** @var string */
    protected $name;

    /** @var InputFilterInterface */
    protected $inputFilter;

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
     * Set input filter
     *
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if (null === $this->inputFilter) {
            $this->inputFilter = new InputFilter();
        }
        return $this->inputFilter;
    }
}
