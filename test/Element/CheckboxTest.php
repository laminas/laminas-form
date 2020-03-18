<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Checkbox as CheckboxElement;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

class CheckboxTest extends TestCase
{
    public function testProvidesValidDefaultValues()
    {
        $element = new CheckboxElement();
        $this->assertEquals('1', $element->getCheckedValue());
        $this->assertEquals('0', $element->getUncheckedValue());
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new CheckboxElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\InArray'
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\InArray':
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

    public function testIsChecked()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());
    }

    public function testSetAttributeValue()
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

    public function testIntegerCheckedValue()
    {
        $element = new CheckboxElement();
        $element->setCheckedValue(123);

        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', 123);
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetChecked()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setChecked(true);
        $this->assertEquals(true, $element->isChecked());

        $element->setChecked(false);
        $this->assertEquals(false, $element->isChecked());
    }

    public function testCheckWithCheckedValue()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setValue($element->getCheckedValue());
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetOptions()
    {
        $element = new CheckboxElement();
        $element->setOptions([
            'use_hidden_element' => true,
            'unchecked_value' => 'foo',
            'checked_value' => 'bar',
        ]);
        $this->assertEquals(true, $element->getOption('use_hidden_element'));
        $this->assertEquals('foo', $element->getOption('unchecked_value'));
        $this->assertEquals('bar', $element->getOption('checked_value'));
    }

    public function testSetOptionsTraversable()
    {
        $element = new CheckboxElement();
        $element->setOptions(new CustomTraversable([
            'use_hidden_element' => true,
            'unchecked_value' => 'foo',
            'checked_value' => 'bar',
        ]));
        $this->assertEquals(true, $element->getOption('use_hidden_element'));
        $this->assertEquals('foo', $element->getOption('unchecked_value'));
        $this->assertEquals('bar', $element->getOption('checked_value'));
    }
}
