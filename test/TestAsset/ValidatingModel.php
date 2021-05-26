<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

class ValidatingModel extends Model implements InputFilterAwareInterface
{
    /** @var null|InputFilterInterface */
    protected $inputFilter;

    /**
     * @inheritDoc
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}
