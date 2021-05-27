<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Range as RangeElement;
use Laminas\I18n\Validator\IsFloat;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Step;
use PHPUnit\Framework\TestCase;

use function extension_loaded;
use function get_class;

class RangeTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $element = new RangeElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            IsFloat::class,
            GreaterThan::class,
            LessThan::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(0, $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(100, $validator->getMax());
                    break;
                case Step::class:
                    $this->assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidator(): void
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
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            IsFloat::class,
            GreaterThan::class,
            LessThan::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(2, $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals(102, $validator->getMax());
                    break;
                case Step::class:
                    $this->assertEquals(2, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }
}
