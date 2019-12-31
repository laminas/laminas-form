<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Number as NumberElement;
use PHPUnit_Framework_TestCase as TestCase;

class NumberTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes()
    {
        if (!extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\Float
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Laminas\I18n\Validator\Float',
            'Laminas\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Laminas\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        if (!extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\Float
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new NumberElement();
        $element->setAttributes(array(
            'inclusive' => true,
            'min'       => 5,
            'max'       => 10,
            'step'      => 1,
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Laminas\I18n\Validator\Float',
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\Step',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(5, $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(10, $validator->getMax());
                    break;
                case 'Laminas\Validator\Step':
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }
}
