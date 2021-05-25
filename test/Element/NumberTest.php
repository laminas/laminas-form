<?php

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Number as NumberElement;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Regex;
use Laminas\Validator\Step;
use PHPUnit\Framework\TestCase;

use function get_class;

class NumberTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes()
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Step::class:
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
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            GreaterThan::class,
            LessThan::class,
            Regex::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(5, $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(10, $validator->getMax());
                    break;
                case Step::class:
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
            if (get_class($validator) == GreaterThan::class) {
                $this->assertFalse($validator->getInclusive());
                break;
            }
        }
    }

    public function testDefaultInclusiveTrueatValidatorWhenInclusiveIsNotSetOnAttributes()
    {
        $element = new NumberElement();
        $element->setAttributes([
            'min' => 5,
        ]);

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if (get_class($validator) == GreaterThan::class) {
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
            if (get_class($validator) == Regex::class) {
                $this->assertFalse($validator->isValid('1,000.01'));
                $this->assertFalse($validator->isValid('-1,000.01'));
                $this->assertTrue($validator->isValid('1000.01'));
                $this->assertTrue($validator->isValid('-1000.01'));
                break;
            }
        }
    }
}
