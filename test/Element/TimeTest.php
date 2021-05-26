<?php

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Time as TimeElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use PHPUnit\Framework\TestCase;

use function get_class;

class TimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '00:00:00',
            'max'       => '00:01:00',
            'step'      => '60',
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
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Date::class:
                    $this->assertEquals('H:i:s', $validator->getFormat());
                    break;
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('00:00:00', $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('00:01:00', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('PT60S');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testFailsWithInvalidMinSpecification(): void
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '00:00',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification(): void
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '00:00',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
