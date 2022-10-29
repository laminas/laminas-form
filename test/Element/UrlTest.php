<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Url as UrlElement;
use Laminas\Validator\Uri;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new UrlElement();
        $element->setAttributes([
            'allowAbsolute' => true,
            'allowRelative' => false,
        ]);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Uri::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Uri::class:
                    self::assertEquals(true, $validator->getAllowAbsolute());
                    self::assertEquals(false, $validator->getAllowRelative());
                    break;
                default:
                    break;
            }
        }
    }
}
