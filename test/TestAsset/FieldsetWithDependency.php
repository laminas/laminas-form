<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use LaminasTest\Form\TestAsset\InputFilter;

class FieldsetWithDependency extends Fieldset
{
    private InputFilter $dependency;

    /**
     * @inheritDoc
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct('fieldset_with_dependency', $options);
    }

    public function init(): void
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

    public function setDependency(InputFilter $dependency): void
    {
        $this->dependency = $dependency;
    }
}
