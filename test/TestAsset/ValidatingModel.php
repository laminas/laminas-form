<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

use function assert;

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
        assert($this->inputFilter !== null);
        return $this->inputFilter;
    }
}
