<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\Date as DateElement;
use Laminas\Form\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function date;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function get_class;

/**
 * @covers \Laminas\Form\Element\Date
 */
class DateTest extends TestCase
{
    /**
     * Stores the original set timezone
     *
     * @var string
     */
    private $originaltimezone;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->originaltimezone = date_default_timezone_get();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        date_default_timezone_set($this->originaltimezone);
    }

    public function testProvidesDefaultInputSpecification()
    {
        $element = new DateElement('foo');

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Date',
            'Laminas\Validator\DateStep',
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\DateStep':
                    $dateInterval = new \DateInterval('P1D');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    $this->assertEquals(date('Y-m-d', 0), $validator->getBaseValue());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new DateElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01',
            'max'       => '2001-01-01',
            'step'      => '1',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Date',
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
                    $this->assertEquals('2000-01-01', $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01-01', $validator->getMax());
                    break;
                case 'Laminas\Validator\DateStep':
                    $dateInterval = new \DateInterval('P1D');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    $this->assertEquals('2000-01-01', $validator->getBaseValue());
                    break;
                default:
                    break;
            }
        }
    }

    public function testValueReturnedFromComposedDateTimeIsRfc3339FullDateFormat()
    {
        $element = new DateElement('foo');
        $date    = new DateTime();
        $element->setValue($date);
        $value   = $element->getValue();
        $this->assertEquals($date->format('Y-m-d'), $value);
    }

    public function testCorrectFormatPassedToDateValidator()
    {
        $element = new DateElement('foo');
        $element->setAttributes([
            'min' => '01-01-2012',
            'max' => '31-12-2012',
        ]);
        $element->setFormat('d-m-Y');

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            switch (get_class($validator)) {
                case 'Laminas\Validator\DateStep':
                case 'Laminas\Validator\Date':
                    $this->assertEquals('d-m-Y', $validator->getFormat());
                    break;
            }
        }
    }

    /**
     * @group 6245
     */
    public function testStepValidatorIgnoresDaylightSavings()
    {
        date_default_timezone_set('Europe/London');

        $element   = new DateElement('foo');

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            switch (get_class($validator)) {
                case 'Laminas\Validator\DateStep':
                    $this->assertTrue($validator->isValid('2013-12-25'));
                    break;
            }
        }
    }

    public function testFailsWithInvalidMinSpecification()
    {
        $element = new DateElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification()
    {
        $element = new DateElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '2001-01-01T00',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
