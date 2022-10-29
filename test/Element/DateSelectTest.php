<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\DateSelect as DateSelectElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

final class DateSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new DateSelectElement();

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Date::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Date::class:
                    self::assertEquals('Y-m-d', $validator->getFormat());
                    break;
                default:
                    break;
            }
        }
    }

    public function testCanSetDateFromDateTime(): void
    {
        $element = new DateSelectElement();
        $element->setValue(new DateTime('2012-09-24'));

        self::assertEquals('2012', $element->getYearElement()->getValue());
        self::assertEquals('09', $element->getMonthElement()->getValue());
        self::assertEquals('24', $element->getDayElement()->getValue());
    }

    public function testCanSetDateFromString(): void
    {
        $element = new DateSelectElement();
        $element->setValue('2012-09-24');

        self::assertEquals('2012', $element->getYearElement()->getValue());
        self::assertEquals('09', $element->getMonthElement()->getValue());
        self::assertEquals('24', $element->getDayElement()->getValue());
    }

    public function testCanGetValue(): void
    {
        $element = new DateSelectElement();
        $element->setValue(new DateTime('2012-09-24'));

        self::assertEquals('2012-09-24', $element->getValue());
    }

    public function testThrowsOnInvalidValue(): void
    {
        $element = new DateSelectElement();
        $this->expectException(InvalidArgumentException::class);
        $element->setValue('hello world');
    }

    public function testConstructAcceptsDayAttributes(): void
    {
        $sut           = new DateSelectElement('dateSelect', ['day_attributes' => ['class' => 'test']]);
        $dayAttributes = $sut->getDayAttributes();
        self::assertEquals('test', $dayAttributes['class']);
    }

    public function testConstructAcceptsTraversableOptions(): void
    {
        $options = new CustomTraversable([
            'day_attributes' => ['class' => 'test'],
        ]);
        $sut     = new DateSelectElement('dateSelect', $options);

        self::assertSame('test', $sut->getDayAttributes()['class']);
    }

    public function testSetOptionsAcceptsTraversableObject(): void
    {
        $options = new CustomTraversable([
            'day_attributes' => ['class' => 'test'],
        ]);
        $sut     = new DateSelectElement();
        $sut->setOptions($options);

        self::assertSame('test', $sut->getDayAttributes()['class']);
    }

    /**
     * @group issue-7114
     */
    public function testValueSetterReturnsSameObjectType(): void
    {
        $element = new DateSelectElement();

        self::assertSame($element, $element->setValue('2014-01-01'));
    }

    public function testNullSetValueIsSemanticallyTodayWithoutEmptyOption(): void
    {
        $element = new DateSelectElement('foo');
        $element->setShouldCreateEmptyOption(false);
        $now = new DateTime();
        $element->setValue(null);
        $value = $element->getValue();
        // the getValue() function returns the date in 'Y-m-d' format
        self::assertEquals($now->format('Y-m-d'), $value);
    }

    public function testNullSetValueIsNullWithEmptyOption(): void
    {
        $element = new DateSelectElement('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setValue(null);
        $value = $element->getValue();
        self::assertEquals(null, $value);
    }
}
