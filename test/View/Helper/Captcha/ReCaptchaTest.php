<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper\Captcha;

use Laminas\Captcha\ReCaptcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\View\Helper\Captcha\ReCaptcha as ReCaptchaHelper;
use LaminasTest\Form\View\Helper\CommonTestCase;

class ReCaptchaTest extends CommonTestCase
{
    protected function setUp()
    {
        if (! getenv('TESTS_LAMINAS_FORM_RECAPTCHA_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_LAMINAS_FORM_RECAPTCHA_SUPPORT to test PDF render');
        }

        if (! class_exists(ReCaptcha::class)) {
            $this->markTestSkipped(
                'laminas-captcha-related tests are skipped until the component '
                . 'is forwards-compatible with laminas-servicemanager v3'
            );
        }

        $this->helper  = new ReCaptchaHelper();
        $this->captcha = new ReCaptcha();
        $this->captcha->setPubKey(getenv('TESTS_LAMINAS_FORM_RECAPTCHA_PUBLIC_KEY'));
        $this->captcha->setPrivKey(getenv('TESTS_LAMINAS_FORM_RECAPTCHA_PRIVATE_KEY'));
        parent::setUp();
    }

    public function getElement()
    {
        $element = new CaptchaElement('foo');
        $element->setCaptcha($this->captcha);
        return $element;
    }

    public function testMissingCaptchaAttributeThrowsDomainException()
    {
        $element = new CaptchaElement('foo');

        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->helper->render($element);
    }

    public function testRendersHiddenInputWhenNameIsNotRecaptchaDefault()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('type="hidden"', $markup);
        $this->assertContains('value="g-recaptcha-response"', $markup);
    }

    public function testDoesNotRenderHiddenInputWhenNameIsRecaptchaDefault()
    {
        $element = $this->getElement();
        $element->setName('g-recaptcha-response');
        $markup  = $this->helper->render($element);
        $this->assertNotContains('type="hidden"', $markup);
    }

    public function testRendersReCaptchaMarkup()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains($this->captcha->getService()->getHtml($element->getName()), $markup);
    }
}
