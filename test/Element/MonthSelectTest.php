<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\MonthSelect as MonthSelectElement;
use PHPUnit\Framework\TestCase;

use function get_class;

class MonthSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new MonthSelectElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Regex',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\Regex':
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
    public function testMonthValidation($value, $expected)
    {
        $element = new MonthSelectElement('foo');
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $monthValidator = $inputSpec['validators'][0];
        $this->assertEquals($expected, $monthValidator->isValid($value));
    }

    public function testCanSetMonthFromDateTime()
    {
        $element  = new MonthSelectElement();
        $element->setValue(new DateTime('2012-09'));

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
    }

    public function testCanGetValue()
    {
        $element  = new MonthSelectElement();
        $element->setValue(new DateTime('2012-09'));
        $this->assertEquals('2012-09', $element->getValue());
    }
}
