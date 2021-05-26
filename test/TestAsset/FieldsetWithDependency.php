<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;

class FieldsetWithDependency extends Fieldset
{
    /** @var InputFilter */
    private $dependency;

    /**
     * @inheritDoc
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct('fieldset_with_dependency', $options);
    }

    public function init()
    {
        // should not fail
        $this->dependency->getValues();
    }

    /**
     * @return InputFilter
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * @param InputFilter $dependency
     */
    public function setDependency($dependency)
    {
        $this->dependency = $dependency;
    }
}
