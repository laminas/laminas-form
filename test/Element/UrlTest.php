<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Url as UrlElement;
use Laminas\Validator\Uri;
use PHPUnit\Framework\TestCase;

use function get_class;

class UrlTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new UrlElement();
        $element->setAttributes([
            'allowAbsolute' => true,
            'allowRelative' => false,
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Uri::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Uri::class:
                    $this->assertEquals(true, $validator->getAllowAbsolute());
                    $this->assertEquals(false, $validator->getAllowRelative());
                    break;
                default:
                    break;
            }
        }
    }
}
