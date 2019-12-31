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
use Laminas\Service\ReCaptcha\ReCaptcha as ReCaptchaService;
use LaminasTest\Form\View\Helper\CommonTestCase;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTest
 */
class ReCaptchaTest extends CommonTestCase
{
    protected $publicKey  = TESTS_LAMINAS_FORM_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_LAMINAS_FORM_RECAPTCHA_PRIVATE_KEY;

    public function setUp()
    {
        if (!constant('TESTS_LAMINAS_FORM_RECAPTCHA_SUPPORT')) {
            $this->markTestSkipped('Enable TESTS_LAMINAS_FORM_RECAPTCHA_SUPPORT to test PDF render');
        }

        $this->helper  = new ReCaptchaHelper();
        $this->captcha = new ReCaptcha(array(
            'sessionClass' => 'LaminasTest\Captcha\TestAsset\SessionContainer',
        ));
        $service = $this->captcha->getService();
        $service->setPublicKey($this->publicKey);
        $service->setPrivateKey($this->privateKey);
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

        $this->setExpectedException('Laminas\Form\Exception\DomainException');
        $this->helper->render($element);
    }

    public function testRendersHiddenInputForChallengeField()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(type="hidden").*?(name="' . $element->getName() . '\[recaptcha_challenge_field\]")#', $markup);
        $this->assertRegExp('#(type="hidden").*?(id="' . $element->getName() . '-challenge")#', $markup);
    }

    public function testRendersHiddenInputForResponseField()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertRegExp('#(type="hidden").*?(name="' . $element->getName() . '\[recaptcha_response_field\]")#', $markup);
        $this->assertRegExp('#(type="hidden").*?(id="' . $element->getName() . '-response")#', $markup);
    }

    public function testRendersReCaptchaMarkup()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains($this->captcha->getService()->getHtml($element->getName()), $markup);
    }

    public function testRendersJsEventScripts()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('function laminasBindEvent', $markup);
        $this->assertContains('document.getElementById("' . $element->getName() . '-challenge")', $markup);
        $this->assertContains('document.getElementById("' . $element->getName() . '-response")', $markup);
    }
}
