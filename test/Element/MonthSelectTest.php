<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\MonthSelect as MonthSelectElement;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function get_class;

final class MonthSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new MonthSelectElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Regex::class:
                    $this->assertEquals('/^[0-9]{4}\-(0?[1-9]|1[012])$/', $validator->getPattern());
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Note about those tests: 2012-1 is not valid in HTML5 validation, but here we use selects, and in some
     * locales, the month may be expressed using only 1 digit, so this is valid here
     *
     * @return array
     */
    public function monthValuesDataProvider()
    {
        return [
            //    value         expected
            ['2012-01',    true],
            ['2012-12',    true],
            ['2012-13',    false],
            ['2012-12-01', false],
            ['12-2012',    false],
            ['2012-1',     true],
            ['12-01',      false],
        ];
    }

    /**
     * @dataProvider monthValuesDataProvider
     */
    public function testMonthValidation(string $value, bool $expected): void
    {
        $element   = new MonthSelectElement('foo');
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $monthValidator = $inputSpec['validators'][0];
        $this->assertEquals($expected, $monthValidator->isValid($value));
    }

    public function testCanSetMonthFromDateTime(): void
    {
        $element = new MonthSelectElement();
        $element->setValue(new DateTime('2012-09'));

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
    }

    public function testCanGetValue(): void
    {
        $element = new MonthSelectElement();
        $element->setValue(new DateTime('2012-09'));
        $this->assertEquals('2012-09', $element->getValue());
    }

    public function testNullSetValueIsSemanticallyTodayWithoutEmptyOption()
    {
        $element = new MonthSelectElement('foo');
        $element->setShouldCreateEmptyOption(false);
        $now = new DateTime();
        $element->setValue(null);
        $value = $element->getValue();
        // the getValue() function returns the date in 'Y-m-d' format
        $this->assertEquals($now->format('Y-m'), $value);
    }

    public function testNullSetValueIsNullWithEmptyOption()
    {
        $element = new MonthSelectElement('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setValue(null);
        $value = $element->getValue();
        $this->assertEquals(null, $value);
    }
}
