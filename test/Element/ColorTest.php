<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Color as ColorElement;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function get_class;

class ColorTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new ColorElement();

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
                    $this->assertEquals('/^#[0-9a-fA-F]{6}$/', $validator->getPattern());
                    break;
                default:
                    break;
            }
        }
    }
}
