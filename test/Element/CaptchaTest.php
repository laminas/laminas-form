<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use ArrayIterator;
use ArrayObject;
use Laminas\Captcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\Factory;
use LaminasTest\Form\TestAsset;
use PHPUnit\Framework\TestCase;

class CaptchaTest extends TestCase
{
    public function testCaptchaIsUndefinedByDefault()
    {
        $element = new CaptchaElement();
        $this->assertNull($element->getCaptcha());
    }

    public function testCaptchaIsMutable()
    {
        $element = new CaptchaElement();

        // by instance
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());

        // by array
        $captcha = [
            'class'   => 'dumb',
        ];
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $element->getCaptcha());

        // by traversable
        $captcha = new ArrayObject([
            'class'   => 'dumb',
        ]);
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $element->getCaptcha());
    }

    public function testCaptchaWithNullRaisesException()
    {
        $element = new CaptchaElement();
        $this->expectException('Laminas\Form\Exception\InvalidArgumentException');
        $element->setCaptcha(null);
    }

    public function testSettingCaptchaSetsCaptchaAttribute()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());
    }

    public function testCreatingCaptchaElementViaFormFactoryWillCreateCaptcha()
    {
        $factory = new Factory();
        $element = $factory->createElement([
            'type'       => 'Laminas\Form\Element\Captcha',
            'name'       => 'foo',
            'options'    => [
                'captcha' => [
                    'class'   => 'dumb'
                ]
            ]
        ]);
        $this->assertInstanceOf('Laminas\Form\Element\Captcha', $element);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $captcha);
    }

    public function testProvidesInputSpecificationThatIncludesCaptchaAsValidator()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb();
        $element->setCaptcha($captcha);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($captcha, $test);
    }

    /**
     * @group 3446
     */
    public function testAllowsPassingTraversableOptionsToConstructor()
    {
        $options = new TestAsset\IteratorAggregate(new ArrayIterator([
            'captcha' => [
                'class'   => 'dumb',
            ],
        ]));
        $element = new CaptchaElement('captcha', $options);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $captcha);
    }
}
