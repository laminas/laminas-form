<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Time as TimeElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
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
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Date::class,
            GreaterThan::class,
            LessThan::class,
            DateStep::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Date::class:
                    self::assertEquals('H:i:s', $validator->getFormat());
                    break;
                case GreaterThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('00:00:00', $validator->getMin());
                    break;
                case LessThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('00:01:00', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('PT60S');
                    self::assertEquals($dateInterval, $validator->getStep());
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
