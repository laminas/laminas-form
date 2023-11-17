<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Email as EmailElement;
use Laminas\Validator\Explode;
use Laminas\Validator\Regex;
use Laminas\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesDefaultValidators(): void
    {
        $element = new EmailElement();

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedValidators = [
            Regex::class,
        ];
        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = $validator::class;
            self::assertEquals($expectedValidators[$i], $class);
        }
    }

    /** @return list<array{0: array<string, scalar>, 1: list<class-string<ValidatorInterface>>}> */
    public static function emailAttributesDataProvider(): array
    {
        return [
                  // attributes               // expectedValidators
            [['multiple' => true], [Explode::class]],
            [['multiple' => false], [Regex::class]],
        ];
    }

    /**
     * @dataProvider emailAttributesDataProvider
     * @param array<string, scalar> $attributes
     * @param list<class-string<ValidatorInterface>> $expectedValidators
     */
    public function testProvidesInputSpecificationBasedOnAttributes(array $attributes, array $expectedValidators): void
    {
        $element = new EmailElement();
        $element->setAttributes($attributes);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators'] ?? null);

        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = $validator::class;
            self::assertEquals($expectedValidators[$i], $class);
            switch ($class) {
                case Explode::class:
                    self::assertInstanceOf(Regex::class, $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }
}
