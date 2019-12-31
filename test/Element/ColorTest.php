<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Color as ColorElement;
use PHPUnit_Framework_TestCase as TestCase;

class ColorTest extends TestCase
{
    public function colorData()
    {
        return [
            ['#012345',     true],
            ['#abcdef',     true],
            ['#012abc',     true],
            ['#012abcd',    false],
            ['#012abcde',   false],
            ['#ABCDEF',     true],
            ['#012ABC',     true],
            ['#bcdefg',     false],
            ['#01a',        false],
            ['01abcd',      false],
            ['blue',        false],
            ['transparent', false],
        ];
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new ColorElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Laminas\Validator\Regex'
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Laminas\Validator\Regex':
                    $this->assertEquals('/^#[0-9a-fA-F]{6}$/', $validator->getPattern());
                    break;
                default:
                    break;
            }
        }
    }
}
