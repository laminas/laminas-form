<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper\Captcha;

use Laminas\Captcha\Figlet as FigletCaptcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\View\Helper\Captcha\Figlet as FigletCaptchaHelper;
use LaminasTest\Form\View\Helper\CommonTestCase;

class FigletTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper  = new FigletCaptchaHelper();
        $this->captcha = new FigletCaptcha();
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

    public function testRendersHiddenInputForId()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(type="hidden")#', $markup);
        $this->assertMatchesRegularExpression(
            '#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(value="' . $this->captcha->getId() . '")#',
            $markup
        );
    }

    public function testRendersTextInputForInput()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#(name="' . $element->getName() . '\&\#x5B\;input\&\#x5D\;").*?(type="text")#', $markup);
    }

    public function testRendersFigletPriorToInputByDefault()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString(
            '<pre>' . $this->captcha->getFiglet()->render($this->captcha->getWord()) . '</pre>'
            . $this->helper->getSeparator() . '<input',
            $markup
        );
    }

    public function testCanRenderFigletFollowingInput()
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('><pre>', $markup);
    }
}
