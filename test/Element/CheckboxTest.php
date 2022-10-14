<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Checkbox as CheckboxElement;
use Laminas\Validator\InArray;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

final class CheckboxTest extends TestCase
{
    public function testProvidesValidDefaultValues(): void
    {
        $element = new CheckboxElement();
        $this->assertEquals('1', $element->getCheckedValue());
        $this->assertEquals('0', $element->getUncheckedValue());
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new CheckboxElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            InArray::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case InArray::class:
                    $this->assertEquals(
                        [$element->getCheckedValue(), $element->getUncheckedValue()],
                        $validator->getHaystack()
                    );
                    break;
                default:
                    break;
            }
        }
    }

    public function testIsChecked(): void
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());
    }

    public function testSetAttributeValue(): void
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', 123);
        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', true);
        $this->assertEquals(true, $element->isChecked());

        $element->setCheckedValue('string_value');
        $element->setChecked(true);
        $this->assertEquals($element->getCheckedValue(), $element->getValue());

        $element->setChecked(false);
        $this->assertEquals($element->getUncheckedValue(), $element->getValue());

        $element->setAttribute('value', 'string_value');
        $this->assertEquals(true, $element->isChecked());

        $element->setAttribute('value', 'another_string');
        $this->assertEquals(false, $element->isChecked());
    }

    public function testIntegerCheckedValue(): void
    {
        $element = new CheckboxElement();
        $element->setCheckedValue('123');

        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', '123');
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetChecked(): void
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setChecked(true);
        $this->assertEquals(true, $element->isChecked());

        $element->setChecked(false);
        $this->assertEquals(false, $element->isChecked());
    }

    public function testCheckWithCheckedValue(): void
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setValue($element->getCheckedValue());
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetOptions(): void
    {
        $element = new CheckboxElement();
        $element->setOptions([
            'use_hidden_element' => true,
            'unchecked_value'    => 'foo',
            'checked_value'      => 'bar',
        ]);
        $this->assertEquals(true, $element->getOption('use_hidden_element'));
        $this->assertEquals('foo', $element->getOption('unchecked_value'));
        $this->assertEquals('bar', $element->getOption('checked_value'));
    }

    public function testSetOptionsTraversable(): void
    {
        $element = new CheckboxElement();
        $element->setOptions(new CustomTraversable([
            'use_hidden_element' => true,
            'unchecked_value'    => 'foo',
            'checked_value'      => 'bar',
        ]));
        $this->assertEquals(true, $element->getOption('use_hidden_element'));
        $this->assertEquals('foo', $element->getOption('unchecked_value'));
        $this->assertEquals('bar', $element->getOption('checked_value'));
    }
}
