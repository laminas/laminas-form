<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;

class FieldsetWithDependency extends Fieldset
{
    /**
     * @var InputFilter
     */
    private $dependency;

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
