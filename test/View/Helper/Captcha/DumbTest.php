<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper\Captcha;

use Laminas\Captcha\Dumb as DumbCaptcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\View\Helper\Captcha\Dumb as DumbCaptchaHelper;
use LaminasTest\Form\View\Helper\CommonTestCase;

class DumbTest extends CommonTestCase
{
    public function setUp()
    {
        if (! class_exists(DumbCaptcha::class)) {
            $this->markTestSkipped(
                'laminas-captcha-related tests are skipped until the component '
                . 'is forwards-compatible with laminas-servicemanager v3'
            );
        }

        $this->helper  = new DumbCaptchaHelper();
        $this->captcha = new DumbCaptcha();
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

    public function testRendersHiddenInputForId()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(type="hidden")#', $markup);
        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(value="' . $this->captcha->getId() . '")#', $markup);
    }

    public function testRendersTextInputForInput()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        $this->assertRegExp('#(name="' . $element->getName() . '\&\#x5B\;input\&\#x5D\;").*?(type="text")#', $markup);
    }

    public function testRendersLabelPriorToInputByDefault()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains($this->captcha->getLabel() . ' <b>' . strrev($this->captcha->getWord()) . '</b>' . $this->helper->getSeparator() . '<input', $markup);
    }

    public function testCanRenderLabelFollowingInput()
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertContains('>' . $this->captcha->getLabel() . ' <b>' . strrev($this->captcha->getWord()) . '</b>' . $this->helper->getSeparator(), $markup);
    }

    public function testSetCaptchaPositionWithNullRaisesException()
    {
        $this->setExpectedException('Laminas\Form\Exception\InvalidArgumentException');
        $this->helper->setCaptchaPosition(null);
    }

    public function testSetSeparator()
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $this->helper->render($element);
        $this->helper->setSeparator('-');

        $this->assertEquals('-', $this->helper->getSeparator());
    }

    public function testRenderSeparatorOneTimeAfterText()
    {
        $element = $this->getElement();
        $this->helper->setSeparator('<br />');
        $markup  = $this->helper->render($element);

        $this->assertContains($this->captcha->getLabel() . ' <b>' . strrev($this->captcha->getWord()) . '</b>' . $this->helper->getSeparator() . '<input name="foo&#x5B;id&#x5D;" type="hidden"', $markup);
        $this->assertNotContains($this->helper->getSeparator() . '<input name="foo[input]" type="text"', $markup);
    }
}
