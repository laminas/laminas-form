<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Week as WeekElement;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

final class WeekTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new WeekElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '1970-W01',
            'max'       => '1970-W03',
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
                    self::assertEquals('1970-W01', $validator->getMin());
                    break;
                case LessThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('1970-W03', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('P1W');
                    self::assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public static function weekValuesDataProvider(): array
    {
        return [
            //    value        expected
            ['2012-W01',  true],
            ['2012-W52',  true],
            ['2012-01',   false],
            ['W12-2012',  false],
            ['2012-W1',   false],
            ['12-W01',    false],
        ];
    }

    /**
     * @dataProvider weekValuesDataProvider
     */
    public function testHTML5WeekValidation(string $value, bool $expected): void
    {
        $element   = new WeekElement('foo');
        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        $weekValidator = $inputSpec['validators'][0];
        self::assertEquals($expected, $weekValidator->isValid($value));
    }
}
