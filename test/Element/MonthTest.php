<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use DateInterval;
use Laminas\Form\Element\Month as MonthElement;
use PHPUnit\Framework\TestCase;

use function get_class;

class MonthTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new MonthElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01',
            'max'       => '2001-01',
            'step'      => '1',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Regex',
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\DateStep',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2000-01', $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01', $validator->getMax());
                    break;
                case 'Laminas\Validator\DateStep':
                    $dateInterval = new DateInterval('P1M');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function monthValuesDataProvider()
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
    public function testHTML5MonthValidation($value, $expected)
    {
        $element = new MonthElement('foo');
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $monthValidator = $inputSpec['validators'][0];
        $this->assertEquals($expected, $monthValidator->isValid($value));
    }
}
