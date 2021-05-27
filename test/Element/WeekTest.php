<?php

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Week as WeekElement;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function get_class;

class WeekTest extends TestCase
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
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
            GreaterThan::class,
            LessThan::class,
            DateStep::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('1970-W01', $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('1970-W03', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('P1W');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function weekValuesDataProvider(): array
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
        $this->assertArrayHasKey('validators', $inputSpec);
        $weekValidator = $inputSpec['validators'][0];
        $this->assertEquals($expected, $weekValidator->isValid($value));
    }
}
