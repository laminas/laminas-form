<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Validator\Explode;
use Laminas\Validator\InArray;
use PHPUnit\Framework\TestCase;

use function count;
use function restore_error_handler;
use function set_error_handler;

use const E_USER_DEPRECATED;

final class MultiCheckboxTest extends TestCase
{
    public static function useHiddenAttributeDataProvider(): array
    {
        return [[true], [false]];
    }

    /**
     * @dataProvider useHiddenAttributeDataProvider
     */
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(bool $useHiddenElement): void
    {
        $element = new MultiCheckboxElement();
        $element->setValueOptions([
            '1' => 'Option 1',
            '2' => 'Option 2',
            '3' => 'Option 3',
        ]);
        $element->setUseHiddenElement($useHiddenElement);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Explode::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Explode::class:
                    $inArrayValidator = $validator->getValidator();
                    self::assertInstanceOf(InArray::class, $inArrayValidator);
                    break;
                default:
                    break;
            }
        }
    }

    public static function multiCheckboxOptionsDataProvider(): array
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
        $element->setValueOptions($options);
        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        $explodeValidator = $inputSpec['validators'][0];
        self::assertInstanceOf(Explode::class, $explodeValidator);
        self::assertTrue($explodeValidator->isValid($valueTests));
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider multiCheckboxOptionsDataProvider
     */
    public function testInArrayValidatorHaystackIsUpdated(array $valueTests, array $options): void
    {
        $element          = new MultiCheckboxElement('my-checkbox');
        $inputSpec        = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0]->getValidator();

        $element->setValueOptions($options);
        $haystack = $inArrayValidator->getHaystack();
        self::assertCount(count($options), $haystack);
    }

    public function testAttributeType(): void
    {
        $element    = new MultiCheckboxElement();
        $attributes = $element->getAttributes();

        self::assertArrayHasKey('type', $attributes);
        self::assertEquals('multi_checkbox', $attributes['type']);
    }

    public function testSetOptionsOptions(): void
    {
        $element = new MultiCheckboxElement();
        $element->setOptions([
            'value_options' => ['bar' => 'baz'],
            'options'       => ['foo' => 'bar'],
        ]);
        self::assertEquals(['bar' => 'baz'], $element->getOption('value_options'));
        self::assertEquals(['foo' => 'bar'], $element->getOption('options'));
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
        self::assertArrayNotHasKey('validators', $inputSpec);
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
        self::assertArrayNotHasKey('Option 2', $valueOptions);
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
        self::assertArrayNotHasKey('Option Undefined', $valueOptions);
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
        self::assertContains($optionValue, $element->getValue());
    }

    /** @deprecated */
    public function testDeprecatedValueOptionsAsAttribute(): void
    {
        $trigger = false;
        set_error_handler(function (int $code, string $message) use (&$trigger): bool {
            self::assertStringContainsString(
                'Providing multi-checkbox value options via attributes is deprecated',
                $message
            );

            $trigger = true;
            return true;
        }, E_USER_DEPRECATED);

        $element = new MultiCheckboxElement();
        $element->setAttributes([
            'options' => [
                'a' => 'A',
                'b' => 'B',
            ],
        ]);

        restore_error_handler();

        self::assertSame([
            'a' => 'A',
            'b' => 'B',
        ], $element->getValueOptions());
        self::assertTrue($trigger);
    }
}
