<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter as RealInputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

use function get_object_vars;

class HydratorStrategyEntityA implements InputFilterAwareInterface
{
    public $entities; // public to make testing easier!
    private $inputFilter; // used to test forms

    public function __construct()
    {
        $this->entities = [];
    }

    public function addEntity(HydratorStrategyEntityB $entity)
    {
        $this->entities[] = $entity;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $input = new Input();
            $input->setName('entities');
            $input->setRequired(false);

            $this->inputFilter = new RealInputFilter();
            $this->inputFilter->add($input);
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    // Add the getArrayCopy method so we can test the ArraySerializable hydrator:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add the populate method so we can test the ArraySerializable hydrator:
    public function populate($data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
