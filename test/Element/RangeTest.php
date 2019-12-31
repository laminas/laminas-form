<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Range as RangeElement;
use PHPUnit_Framework_TestCase as TestCase;

class RangeTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes()
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new RangeElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\I18n\Validator\IsFloat',
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\Step',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(0, $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(100, $validator->getMax());
                    break;
                case 'Laminas\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new RangeElement();
        $element->setAttributes([
            'inclusive' => true,
            'min'       => 2,
            'max'       => 102,
            'step'      => 2,
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\I18n\Validator\IsFloat',
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\Step',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(2, $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(102, $validator->getMax());
                    break;
                case 'Laminas\Validator\Step':
                    $this->assertEquals(2, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }
}
