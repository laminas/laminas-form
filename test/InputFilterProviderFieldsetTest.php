<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\InputFilterProviderFieldset;
use PHPUnit\Framework\TestCase;

class InputFilterProviderFieldsetTest extends TestCase
{
    public function setUp()
    {
        $this->fieldset = new InputFilterProviderFieldset();
    }

    public function testCanSetInputFilterSpec()
    {
        $filterSpec = ['filter' => ['filter_options']];

        $this->fieldset->setInputFilterSpecification($filterSpec);
        $this->assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testCanSetInputFilterSpecViaOptions()
    {
        $filterSpec = ['filter' => ['filter_options']];

        $this->fieldset->setOptions(['input_filter_spec' => $filterSpec]);
        $this->assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testFilterSpecIsInitiallyEmpty()
    {
        $this->assertEmpty($this->fieldset->getInputFilterSpecification());
    }
}
