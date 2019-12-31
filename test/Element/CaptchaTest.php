<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use ArrayObject;
use Laminas\Captcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\Factory;
use PHPUnit_Framework_TestCase as TestCase;

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
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());

        // by array
        $captcha = array(
            'class'   => 'dumb',
            'options' => array(
                'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
            )
        );
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $element->getCaptcha());

        // by traversable
        $captcha = new ArrayObject(array(
            'class'   => 'dumb',
            'options' => array(
                    'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
            )
        ));
        $element->setCaptcha($captcha);
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $element->getCaptcha());
    }

    public function testSettingCaptchaSetsCaptchaAttribute()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);
        $this->assertSame($captcha, $element->getCaptcha());
    }

    public function testCreatingCaptchaElementViaFormFactoryWillCreateCaptcha()
    {
        $factory = new Factory();
        $element = $factory->createElement(array(
            'type'       => 'Laminas\Form\Element\Captcha',
            'name'       => 'foo',
            'options'    => array(
                'captcha' => array(
                    'class'   => 'dumb',
                    'options' => array(
                        'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
                    )
                )
            )
        ));
        $this->assertInstanceOf('Laminas\Form\Element\Captcha', $element);
        $captcha = $element->getCaptcha();
        $this->assertInstanceOf('Laminas\Captcha\Dumb', $captcha);
    }

    public function testProvidesInputSpecificationThatIncludesCaptchaAsValidator()
    {
        $element = new CaptchaElement();
        $captcha = new Captcha\Dumb(array(
            'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
        ));
        $element->setCaptcha($captcha);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($captcha, $test);
    }
}
