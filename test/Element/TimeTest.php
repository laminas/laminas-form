<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Time as TimeElement;
use Laminas\Form\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function get_class;

class TimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '00:00:00',
            'max'       => '00:01:00',
            'step'      => '60',
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
                case 'Laminas\Validator\Date':
                    $this->assertEquals('H:i:s', $validator->getFormat());
                    break;
                case 'Laminas\Validator\GreaterThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('00:00:00', $validator->getMin());
                    break;
                case 'Laminas\Validator\LessThan':
                    $this->assertTrue($validator->getInclusive());
                    $this->assertEquals('00:01:00', $validator->getMax());
                    break;
                case 'Laminas\Validator\DateStep':
                    $dateInterval = new \DateInterval('PT60S');
                    $this->assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testFailsWithInvalidMinSpecification()
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '00:00',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification()
    {
        $element = new TimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '00:00',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
