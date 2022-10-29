<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Color as ColorElement;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

final class ColorTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new ColorElement();

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Regex::class:
                    self::assertEquals('/^#[0-9a-fA-F]{6}$/', $validator->getPattern());
                    break;
                default:
                    break;
            }
        }
    }
}
