<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Validator\Explode;
use Laminas\Validator\InArray;
use PHPUnit\Framework\TestCase;

use function count;
use function get_class;

final class MultiCheckboxTest extends TestCase
{
    public function useHiddenAttributeDataProvider(): array
    {
        return [[true], [false]];
    }

    /**
     * @dataProvider useHiddenAttributeDataProvider
     */
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(bool $useHiddenElement): void
    {
        $element = new MultiCheckboxElement();
        $options = [
            '1' => 'Option 1',
            '2' => 'Option 2',
            '3' => 'Option 3',
        ];
        $element->setAttributes([
            'options' => $options,
        ]);
        $element->setUseHiddenElement($useHiddenElement);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Explode::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Explode::class:
                    $inArrayValidator = $validator->getValidator();
                    $this->assertInstanceOf(InArray::class, $inArrayValidator);
                    break;
                default:
                    break;
            }
        }
    }

    public function multiCheckboxOptionsDataProvider(): array
    {
        return [
            [
                ['foo', 'bar'],
                [
                    'foo' => 'My Foo Label',
                    'bar' => 'My Bar Label',
                ],
            ],
            [
                ['foo', 'bar'],
                [
                    0 => ['label' => 'My Foo Label', 'value' => 'foo'],
                    1 => ['label' => 'My Bar Label', 'value' => 'bar'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider multiCheckboxOptionsDataProvider
     */
    public function testInArrayValidationOfOptions(array $valueTests, array $options): void
    {
        $element = new MultiCheckboxElement('my-checkbox');
        $element->setAttributes([
            'options' => $options,
        ]);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $explodeValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf(Explode::class, $explodeValidator);
        $this->assertTrue($explodeValidator->isValid($valueTests));
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider multiCheckboxOptionsDataProvider
     */
    public function testInArrayValidatorHaystakIsUpdated(array $valueTests, array $options): void
    {
        $element          = new MultiCheckboxElement('my-checkbox');
        $inputSpec        = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0]->getValidator();

        $element->setAttributes([
            'options' => $options,
        ]);
        $haystack = $inArrayValidator->getHaystack();
        $this->assertCount(count($options), $haystack);
    }

    public function testAttributeType(): void
    {
        $element    = new MultiCheckboxElement();
        $attributes = $element->getAttributes();

        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('multi_checkbox', $attributes['type']);
    }

    public function testSetOptionsOptions(): void
    {
        $element = new MultiCheckboxElement();
        $element->setOptions([
            'value_options' => ['bar' => 'baz'],
            'options'       => ['foo' => 'bar'],
        ]);
        $this->assertEquals(['bar' => 'baz'], $element->getOption('value_options'));
        $this->assertEquals(['foo' => 'bar'], $element->getOption('options'));
    }

    public function testDisableInputSpecification(): void
    {
        $element = new MultiCheckboxElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->setDisableInArrayValidator(true);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayNotHasKey('validators', $inputSpec);
    }

    public function testUnsetValueOption(): void
    {
        $element = new MultiCheckboxElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->unsetValueOption('Option 2');

        $valueOptions = $element->getValueOptions();
        $this->assertArrayNotHasKey('Option 2', $valueOptions);
    }

    public function testUnsetUndefinedValueOption(): void
    {
        $element = new MultiCheckboxElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->unsetValueOption('Option Undefined');

        $valueOptions = $element->getValueOptions();
        $this->assertArrayNotHasKey('Option Undefined', $valueOptions);
    }

    public function testOptionValueinSelectedOptions(): void
    {
        $element = new MultiCheckboxElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);

        $optionValue     = 'option3';
        $selectedOptions = ['option1', 'option3'];
        $element->setValue($selectedOptions);
        $this->assertContains($optionValue, $element->getValue());
    }
}
