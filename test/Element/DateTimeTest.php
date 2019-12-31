<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\DateTime as DateTimeElement;
use PHPUnit_Framework_TestCase as TestCase;

class DateTimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes(array(
            'inclusive' => true,
            'min'       => '2000-01-01T00:00:00Z',
            'max'       => '2001-01-01T00:00:00Z',
            'step'      => '1',
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Laminas\Validator\Date',
            'Laminas\Validator\GreaterThan',
            'Laminas\Validator\LessThan',
            'Laminas\Validator\DateStep',
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2000-01-01T00:00:00Z', $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01-01T00:00:00Z', $validator->getMax());
                    break;
                case 'Laminas\Validator\DateStep':
                    $dateInterval = new \DateInterval('PT1M');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testUsesRfc3339FormatByDefault()
    {
        $element = new DateTimeElement('foo');
        $this->assertEquals(DateTime::RFC3339, $element->getFormat());
    }

    public function testSpecifyingADateTimeValueWillReturnRfc3339FormattedStringByDefault()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertEquals($date->format($date::RFC3339), $element->getValue());
    }

    public function testValueIsFormattedAccordingToFormatInElement()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setFormat($date::RFC2822);
        $element->setValue($date);
        $this->assertEquals($date->format($date::RFC2822), $element->getValue());
    }

    public function testCanRetrieveDateTimeObjectByPassingBooleanFalseToGetValue()
    {
        $date = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertSame($date, $element->getValue(false));
    }
}
