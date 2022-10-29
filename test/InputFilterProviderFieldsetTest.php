<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\InputFilterProviderFieldset;
use PHPUnit\Framework\TestCase;

final class InputFilterProviderFieldsetTest extends TestCase
{
    private InputFilterProviderFieldset $fieldset;

    protected function setUp(): void
    {
        $this->fieldset = new InputFilterProviderFieldset();
    }

    public function testCanSetInputFilterSpec(): void
    {
        $filterSpec = ['filter' => ['filter_options']];

        $this->fieldset->setInputFilterSpecification($filterSpec);
        self::assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testCanSetInputFilterSpecViaOptions(): void
    {
        $filterSpec = ['filter' => ['filter_options']];

        $this->fieldset->setOptions(['input_filter_spec' => $filterSpec]);
        self::assertEquals($filterSpec, $this->fieldset->getInputFilterSpecification());
    }

    public function testFilterSpecIsInitiallyEmpty(): void
    {
        self::assertEmpty($this->fieldset->getInputFilterSpecification());
    }
}
