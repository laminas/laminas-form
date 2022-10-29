<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Radio as RadioElement;
use Laminas\Validator\InArray;
use PHPUnit\Framework\TestCase;

final class RadioTest extends TestCase
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
        $element = new RadioElement();
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
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            InArray::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
        }
    }

    public function radioOptionsDataProvider(): array
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
     * @dataProvider radioOptionsDataProvider
     */
    public function testInArrayValidationOfOptions(array $valueTests, array $options): void
    {
        $element = new RadioElement('my-radio');
        $element->setAttributes([
            'options' => $options,
        ]);
        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        self::assertInstanceOf(InArray::class, $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            self::assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    public function testDisableInputSpecification(): void
    {
        $element = new RadioElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->setDisableInArrayValidator(true);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayNotHasKey('validators', $inputSpec);
    }
}
