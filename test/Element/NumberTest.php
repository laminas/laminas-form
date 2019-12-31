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
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Regex',
            'Laminas\Validator\Step',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
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
        $element = new NumberElement();
        $element->setAttributes([
            'inclusive' => true,
            'min'       => 5,
            'max'       => 10,
            'step'      => 1,
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\Regex',
            'Laminas\Validator\Step',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
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

    public function testFalseInclusiveValidatorBasedOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes([
            'inclusive' => false,
            'min'       => 5,
        ]);

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Laminas\Validator\GreaterThan') {
                $this->assertFalse($validator->getInclusive());
                break;
            }
        }
    }

    public function testDefaultInclusiveTrueatValidatorWhenInclusiveIsNotSetOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes([
            'min'       => 5,
        ]);

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Laminas\Validator\GreaterThan') {
                $this->assertTrue($validator->getInclusive());
                break;
            }
        }
    }

    public function testOnlyCastableDecimalsAreAccepted()
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if (get_class($validator) == 'Laminas\Validator\Regex') {
                $this->assertFalse($validator->isValid('1,000.01'));
                $this->assertFalse($validator->isValid('-1,000.01'));
                $this->assertTrue($validator->isValid('1000.01'));
                $this->assertTrue($validator->isValid('-1000.01'));
                break;
            }
        }
    }
}
