<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Radio as RadioElement;
use PHPUnit\Framework\TestCase;

use function get_class;

class RadioTest extends TestCase
{
    public function useHiddenAttributeDataProvider()
    {
        return [[true], [false]];
    }

    /**
     * @dataProvider useHiddenAttributeDataProvider
     */
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes($useHiddenElement)
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
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\InArray',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
        }
    }

    public function radioOptionsDataProvider()
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
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new RadioElement('my-radio');
        $element->setAttributes([
            'options' => $options,
        ]);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Laminas\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    public function testDisableInputSpecification()
    {
        $element = new RadioElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->setDisableInArrayValidator(true);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayNotHasKey('validators', $inputSpec);
    }
}
