<?php

namespace LaminasTest\Form\Element;

use DateTime;
use Laminas\Form\Element\DateSelect as DateSelectElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

use function get_class;

class DateSelectTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new DateSelectElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Date::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Date::class:
                    $this->assertEquals('Y-m-d', $validator->getFormat());
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

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
    }

    public function testCanSetDateFromString(): void
    {
        $element = new DateSelectElement();
        $element->setValue('2012-09-24');

        $this->assertEquals('2012', $element->getYearElement()->getValue());
        $this->assertEquals('09', $element->getMonthElement()->getValue());
        $this->assertEquals('24', $element->getDayElement()->getValue());
    }

    public function testCanGetValue(): void
    {
        $element = new DateSelectElement();
        $element->setValue(new DateTime('2012-09-24'));

        $this->assertEquals('2012-09-24', $element->getValue());
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
        $this->assertEquals('test', $dayAttributes['class']);
    }

    public function testConstructAcceptsTraversableOptions(): void
    {
        $options = new CustomTraversable([
            'day_attributes' => ['class' => 'test'],
        ]);
        $sut     = new DateSelectElement('dateSelect', $options);

        $this->assertSame('test', $sut->getDayAttributes()['class']);
    }

    public function testSetOptionsAcceptsTraversableObject(): void
    {
        $options = new CustomTraversable([
            'day_attributes' => ['class' => 'test'],
        ]);
        $sut     = new DateSelectElement();
        $sut->setOptions($options);

        $this->assertSame('test', $sut->getDayAttributes()['class']);
    }

    /**
     * @group issue-7114
     */
    public function testValueSetterReturnsSameObjectType(): void
    {
        $element = new DateSelectElement();

        $this->assertSame($element, $element->setValue('2014-01-01'));
    }
}
