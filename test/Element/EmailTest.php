<?php

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Email as EmailElement;
use Laminas\Validator\Explode;
use Laminas\Validator\Regex;
use PHPUnit\Framework\TestCase;

use function get_class;

class EmailTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesDefaultValidators()
    {
        $element = new EmailElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedValidators = [
            Regex::class,
        ];
        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
        }
    }

    public function emailAttributesDataProvider(): array
    {
        return [
                  // attributes               // expectedValidators
            [['multiple' => true], [Explode::class]],
            [['multiple' => false], [Regex::class]],
        ];
    }

    /**
     * @dataProvider emailAttributesDataProvider
     */
    public function testProvidesInputSpecificationBasedOnAttributes(array $attributes, array $expectedValidators)
    {
        $element = new EmailElement();
        $element->setAttributes($attributes);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
            switch ($class) {
                case Explode::class:
                    $this->assertInstanceOf(Regex::class, $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }
}
