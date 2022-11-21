<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Csrf as CsrfElement;
use Laminas\Validator\Csrf;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new CsrfElement('foo');

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Csrf::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Csrf::class:
                    self::assertEquals('foo', $validator->getName());
                    break;
                default:
                    break;
            }
        }
    }

    public function testAllowSettingCustomCsrfValidator(): void
    {
        $element       = new CsrfElement('foo');
        $validatorMock = $this->createMock(Csrf::class);
        $element->setCsrfValidator($validatorMock);
        self::assertEquals($validatorMock, $element->getCsrfValidator());
    }

    public function testAllowSettingCsrfValidatorOptions(): void
    {
        $element = new CsrfElement('foo');
        $element->setCsrfValidatorOptions(['timeout' => 777]);
        $validator = $element->getCsrfValidator();
        self::assertEquals('foo', $validator->getName());
        self::assertEquals(777, $validator->getTimeout());
    }

    public function testAllowSettingCsrfOptions(): void
    {
        $element = new CsrfElement('foo');
        $element->setOptions([
            'csrf_options' => [
                'timeout' => 777,
                'salt'    => 'MySalt',
            ],
        ]);
        $validator = $element->getCsrfValidator();
        self::assertEquals('foo', $validator->getName());
        self::assertEquals(777, $validator->getTimeOut());
        self::assertEquals('MySalt', $validator->getSalt());
    }

    public function testSetOptionsTraversable(): void
    {
        $element = new CsrfElement('foo');
        $element->setOptions(new CustomTraversable([
            'csrf_options' => [
                'timeout' => 777,
                'salt'    => 'MySalt',
            ],
        ]));
        $validator = $element->getCsrfValidator();
        self::assertEquals('foo', $validator->getName());
        self::assertEquals(777, $validator->getTimeOut());
        self::assertEquals('MySalt', $validator->getSalt());
    }

    public function testNameOverride(): void
    {
        $element = new CsrfElement('foo');
        $element->setOptions([
            'csrf_options' => [
                'name' => 'bar',
            ],
        ]);
        $validator = $element->getCsrfValidator();
        self::assertEquals('foo', $element->getName());
        self::assertEquals('bar', $validator->getName());
        self::assertEquals('Laminas_Validator_Csrf_salt_bar', $validator->getSessionName());
    }
}
