<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\DateTimeLocal as DateTimeLocalElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use PHPUnit\Framework\TestCase;

final class DateTimeLocalTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new DateTimeLocalElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00:00',
            'max'       => '2001-01-01T00:00',
            'step'      => '1',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Date::class,
            GreaterThan::class,
            LessThan::class,
            DateStep::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2000-01-01T00:00', $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01-01T00:00', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('PT1M');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testFailsWithInvalidMinSpecification(): void
    {
        $element = new DateTimeLocalElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2001-01-01T00:00Z',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification(): void
    {
        $element = new DateTimeLocalElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '2001-01-01T00:00Z',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
