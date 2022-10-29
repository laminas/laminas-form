<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Month as MonthElement;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

final class MonthTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new MonthElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01',
            'max'       => '2001-01',
            'step'      => '1',
        ]);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
            GreaterThan::class,
            LessThan::class,
            DateStep::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('2000-01', $validator->getMin());
                    break;
                case LessThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('2001-01', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('P1M');
                    self::assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function monthValuesDataProvider(): array
    {
        return [
            //    value         expected
            ['2012-01',    true],
            ['2012-12',    true],
            ['2012-13',    false],
            ['2012-12-01', false],
            ['12-2012',    false],
            ['2012-1',     false],
            ['12-01',      false],
        ];
    }

    /**
     * @dataProvider monthValuesDataProvider
     */
    public function testHTML5MonthValidation(string $value, bool $expected): void
    {
        $element   = new MonthElement('foo');
        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        $monthValidator = $inputSpec['validators'][0];
        self::assertEquals($expected, $monthValidator->isValid($value));
    }
}
