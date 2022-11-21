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
        self::assertEquals('1', $element->getCheckedValue());
        self::assertEquals('0', $element->getUncheckedValue());
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new CheckboxElement();

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            InArray::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case InArray::class:
                    self::assertEquals(
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
        self::assertEquals(false, $element->isChecked());
    }

    public function testSetAttributeValue(): void
    {
        $element = new CheckboxElement();
        self::assertEquals(false, $element->isChecked());

        $element->setAttribute('value', 123);
        self::assertEquals(false, $element->isChecked());

        $element->setAttribute('value', true);
        self::assertEquals(true, $element->isChecked());

        $element->setCheckedValue('string_value');
        $element->setChecked(true);
        self::assertEquals($element->getCheckedValue(), $element->getValue());

        $element->setChecked(false);
        self::assertEquals($element->getUncheckedValue(), $element->getValue());

        $element->setAttribute('value', 'string_value');
        self::assertEquals(true, $element->isChecked());

        $element->setAttribute('value', 'another_string');
        self::assertEquals(false, $element->isChecked());
    }

    public function testIntegerCheckedValue(): void
    {
        $element = new CheckboxElement();
        $element->setCheckedValue('123');

        self::assertEquals(false, $element->isChecked());

        $element->setAttribute('value', '123');
        self::assertEquals(true, $element->isChecked());
    }

    public function testSetChecked(): void
    {
        $element = new CheckboxElement();
        self::assertEquals(false, $element->isChecked());

        $element->setChecked(true);
        self::assertEquals(true, $element->isChecked());

        $element->setChecked(false);
        self::assertEquals(false, $element->isChecked());
    }

    public function testCheckWithCheckedValue(): void
    {
        $element = new CheckboxElement();
        self::assertEquals(false, $element->isChecked());

        $element->setValue($element->getCheckedValue());
        self::assertEquals(true, $element->isChecked());
    }

    public function testSetOptions(): void
    {
        $element = new CheckboxElement();
        $element->setOptions([
            'use_hidden_element' => true,
            'unchecked_value'    => 'foo',
            'checked_value'      => 'bar',
        ]);
        self::assertEquals(true, $element->getOption('use_hidden_element'));
        self::assertEquals('foo', $element->getOption('unchecked_value'));
        self::assertEquals('bar', $element->getOption('checked_value'));
    }

    public function testSetOptionsTraversable(): void
    {
        $element = new CheckboxElement();
        $element->setOptions(new CustomTraversable([
            'use_hidden_element' => true,
            'unchecked_value'    => 'foo',
            'checked_value'      => 'bar',
        ]));
        self::assertEquals(true, $element->getOption('use_hidden_element'));
        self::assertEquals('foo', $element->getOption('unchecked_value'));
        self::assertEquals('bar', $element->getOption('checked_value'));
    }
}
