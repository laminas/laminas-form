<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use DateTime;
use Laminas\Filter\DateTimeFormatter;
use Laminas\Form\Element\DateTime as DateTimeElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use PHPUnit\Framework\TestCase;

use function get_class;

final class DateTimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00:00Z',
            'max'       => '2001-01-01T00:00Z',
            'step'      => '1',
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
                case GreaterThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2000-01-01T00:00Z', $validator->getMin());
                    break;
                case LessThan::class:
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('2001-01-01T00:00Z', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('PT1M');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesDateTimeFormatterBasedOnAttributes(): void
    {
        $element = new DateTimeElement('foo');
        $element->setFormat(DateTime::W3C);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('filters', $inputSpec);
        $this->assertIsArray($inputSpec['filters']);

        foreach ($inputSpec['filters'] as $filter) {
            switch ($filter['name']) {
                case DateTimeFormatter::class:
                    $this->assertEquals($filter['options']['format'], DateTime::W3C);
                    $this->assertEquals($filter['options']['format'], $element->getFormat());
                    break;
                default:
                    break;
            }
        }
    }

    public function testUsesBrowserFormatByDefault(): void
    {
        $element = new DateTimeElement('foo');
        $this->assertEquals(DateTimeElement::DATETIME_FORMAT, $element->getFormat());
    }

    public function testSpecifyingADateTimeValueWillReturnBrowserFormattedStringByDefault(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertEquals($date->format(DateTimeElement::DATETIME_FORMAT), $element->getValue());
    }

    public function testValueIsFormattedAccordingToFormatInElement(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setFormat($date::RFC2822);
        $element->setValue($date);
        $this->assertEquals($date->format($date::RFC2822), $element->getValue());
    }

    public function testCanRetrieveDateTimeObjectByPassingBooleanFalseToGetValue(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        $this->assertSame($date, $element->getValue(false));
    }

    public function testSetFormatWithOptions(): void
    {
        $format  = 'Y-m-d';
        $element = new DateTimeElement('foo');
        $element->setOptions([
            'format' => $format,
        ]);

        $this->assertSame($format, $element->getFormat());
    }

    public function testFailsWithInvalidMinSpecification(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '2001-01-01T00',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
