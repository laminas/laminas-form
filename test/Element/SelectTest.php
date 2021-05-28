<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Select as SelectElement;
use Laminas\Validator\Explode;
use Laminas\Validator\InArray;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

use function count;
use function get_class;

final class SelectTest extends TestCase
{
    public function testProvidesInputSpecificationForSingleSelect(): void
    {
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            InArray::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
        }
    }

    public function testValidateWorksForNestedSelectElementWithSimpleNaming(): void
    {
        $element = new SelectElement();
        $element->setValueOptions([
            [
                'label'   => 'group 1',
                'options' => [
                    'Option 1' => 'Label 1',
                    'Option 2' => 'Label 2',
                    'Option 3' => 'Label 2',
                ],
            ],
        ]);

        $inputSpec        = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }

    public function testValidateWorksForNestedSelectElementWithExplicitNaming(): void
    {
        $element = new SelectElement();
        $element->setValueOptions([
            [
                'label'   => 'group 1',
                'options' => [
                    ['value' => 'Option 1', 'label' => 'Label 1'],
                    ['value' => 'Option 2', 'label' => 'Label 2'],
                    ['value' => 'Option 3', 'label' => 'Label 3'],
                ],
            ],
        ]);

        $inputSpec        = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertTrue($inArrayValidator->isValid('Option 2'));
        $this->assertTrue($inArrayValidator->isValid('Option 3'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }

    public function testProvidesInputSpecificationForMultipleSelect(): void
    {
        $element = new SelectElement();
        $element->setAttributes([
            'multiple' => true,
        ]);
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);

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
                    $this->assertInstanceOf(InArray::class, $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }

    public function selectOptionsDataProvider(): array
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
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidationOfOptions(array $valueTests, array $options): void
    {
        $element = new SelectElement('my-select');
        $element->setValueOptions($options);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf(InArray::class, $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidatorHaystakIsUpdated(array $valueTests, array $options): void
    {
        $element   = new SelectElement('my-select');
        $inputSpec = $element->getInputSpecification();

        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf(InArray::class, $inArrayValidator);

        $element->setValueOptions($options);
        $haystack = $inArrayValidator->getHaystack();
        $this->assertCount(count($options), $haystack);
    }

    public function testOptionsHasArrayOnConstruct(): void
    {
        $element = new SelectElement();
        $this->assertIsArray($element->getValueOptions());
    }

    public function testDeprecateOptionsInAttributes(): void
    {
        $element      = new SelectElement();
        $valueOptions = [
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ];
        $element->setAttributes([
            'multiple' => true,
            'options'  => $valueOptions,
        ]);
        $this->assertEquals($valueOptions, $element->getValueOptions());
    }

    public function testSetOptionsArray(): void
    {
        $element = new SelectElement();
        $element->setOptions([
            'value_options' => ['bar' => 'baz'],
            'options'       => ['foo' => 'bar'],
            'empty_option'  => 'xye',
        ]);
        $this->assertEquals(['bar' => 'baz'], $element->getOption('value_options'));
        $this->assertEquals(['foo' => 'bar'], $element->getOption('options'));
        $this->assertEquals('xye', $element->getOption('empty_option'));
    }

    public function testSetOptionsTraversable(): void
    {
        $element = new SelectElement();
        $element->setOptions(new CustomTraversable([
            'value_options' => ['bar' => 'baz'],
            'options'       => ['foo' => 'bar'],
            'empty_option'  => 'xye',
        ]));
        $this->assertEquals(['bar' => 'baz'], $element->getOption('value_options'));
        $this->assertEquals(['foo' => 'bar'], $element->getOption('options'));
        $this->assertEquals('xye', $element->getOption('empty_option'));
    }

    public function testDisableInputSpecification(): void
    {
        $element = new SelectElement();
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
        $element = new SelectElement();
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
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->unsetValueOption('Option Undefined');

        $valueOptions = $element->getValueOptions();
        $this->assertArrayNotHasKey('Option Undefined', $valueOptions);
    }

    public function testSetOptionsToSelectMultiple(): void
    {
        $element = new SelectElement(null, [
            'label'              => 'Importance',
            'use_hidden_element' => true,
            'unselected_value'   => 'empty',
            'value_options'      => [
                'foo' => 'Foo',
                'bar' => 'Bar',
            ],
        ]);
        $element->setAttributes(['multiple' => 'multiple']);

        $this->assertTrue($element->isMultiple());
        $this->assertTrue($element->useHiddenElement());
        $this->assertEquals('empty', $element->getUnselectedValue());
    }

    public function testProvidesInputSpecificationForMultipleSelectWithUseHiddenElement(): void
    {
        $element = new SelectElement();
        $element
            ->setUseHiddenElement(true)
            ->setAttributes([
                'multiple' => true,
            ]);

        $inputSpec = $element->getInputSpecification();

        $this->assertArrayHasKey('allow_empty', $inputSpec);
        $this->assertTrue($inputSpec['allow_empty']);
        $this->assertArrayHasKey('continue_if_empty', $inputSpec);
        $this->assertTrue($inputSpec['continue_if_empty']);
    }
}
