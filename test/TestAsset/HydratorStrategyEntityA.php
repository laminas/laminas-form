<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter as RealInputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

use function get_object_vars;

class HydratorStrategyEntityA implements InputFilterAwareInterface
{
    /** @var HydratorStrategyEntityB[]  */
    public $entities = []; // public to make testing easier!
    /** @var null|InputFilterInterface */
    private $inputFilter; // used to test forms

    public function addEntity(HydratorStrategyEntityB $entity)
    {
        $this->entities[] = $entity;
    }

    /**
     * @return HydratorStrategyEntityB[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @param HydratorStrategyEntityB[] $entities
     */
    public function setEntities(array $entities): void
    {
        $this->entities = $entities;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Add the getArrayCopy method so we can test the ArraySerializable hydrator
     */
    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Add the populate method so we can test the ArraySerializable hydrator
     */
    public function populate(array $data): void
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
