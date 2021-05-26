<?php

namespace LaminasTest\Form;

use Laminas\Form\InputFilterProviderFieldset;
use PHPUnit\Framework\TestCase;

class InputFilterProviderFieldsetTest extends TestCase
{
    /** @var InputFilterProviderFieldset */
    private $fieldset;

    protected function setUp(): void
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
