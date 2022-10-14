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
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Csrf::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Csrf::class:
                    $this->assertEquals('foo', $validator->getName());
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
        $this->assertEquals($validatorMock, $element->getCsrfValidator());
    }

    public function testAllowSettingCsrfValidatorOptions(): void
    {
        $element = new CsrfElement('foo');
        $element->setCsrfValidatorOptions(['timeout' => 777]);
        $validator = $element->getCsrfValidator();
        $this->assertEquals('foo', $validator->getName());
        $this->assertEquals(777, $validator->getTimeout());
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
        $this->assertEquals('foo', $validator->getName());
        $this->assertEquals(777, $validator->getTimeOut());
        $this->assertEquals('MySalt', $validator->getSalt());
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
        $this->assertEquals('foo', $validator->getName());
        $this->assertEquals(777, $validator->getTimeOut());
        $this->assertEquals('MySalt', $validator->getSalt());
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
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('bar', $validator->getName());
        $this->assertEquals('Laminas_Validator_Csrf_salt_bar', $validator->getSessionName());
    }
}
