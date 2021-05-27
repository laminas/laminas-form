<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use ArrayIterator;
use ArrayObject;
use Laminas\Captcha;
use Laminas\Captcha\Dumb;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use LaminasTest\Form\TestAsset;
use PHPUnit\Framework\TestCase;

use function array_shift;

class CaptchaTest extends TestCase
{
    public function testCaptchaIsUndefinedByDefault(): void
    {
        $element = new CaptchaElement();
        $this->assertNull($element->getCaptcha());
    }

    public function testCaptchaIsMutable(): void
    {
        $element = new CaptchaElement();

        // by instance
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());

        // by array
        $captcha = [
            'class' => 'dumb',
        ];
        $element->setCaptcha($captcha);
        $this->assertInstanceOf(Dumb::class, $element->getCaptcha());

        // by traversable
        $captcha = new ArrayObject([
            'class' => 'dumb',
        ]);
        $element->setCaptcha($captcha);
        $this->assertInstanceOf(Dumb::class, $element->getCaptcha());
    }

    public function testCaptchaWithNullRaisesException(): void
    {
        $element = new CaptchaElement();
        $this->expectException(InvalidArgumentException::class);
        $element->setCaptcha(null);
    }

    public function testSettingCaptchaSetsCaptchaAttribute(): void
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());
    }

    public function testCreatingCaptchaElementViaFormFactoryWillCreateCaptcha(): void
    {
        $factory = new Factory();
        $element = $factory->createElement([
            'type'    => CaptchaElement::class,
            'name'    => 'foo',
            'options' => [
                'captcha' => [
                    'class' => 'dumb',
                ],
            ],
        ]);
        $this->assertInstanceOf(CaptchaElement::class, $element);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf(Dumb::class, $captcha);
    }

    public function testProvidesInputSpecificationThatIncludesCaptchaAsValidator(): void
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($captcha, $test);
    }

    /**
     * @group issue-3446
     */
    public function testAllowsPassingTraversableOptionsToConstructor(): void
    {
        $options = new TestAsset\IteratorAggregate(new ArrayIterator([
            'captcha' => [
                'class' => 'dumb',
            ],
        ]));
        $element = new CaptchaElement('captcha', $options);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf(Dumb::class, $captcha);
    }
}
