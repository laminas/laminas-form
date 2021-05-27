<?php

namespace LaminasTest\Form\View\Helper\Captcha;

use Laminas\Captcha\Figlet as FigletCaptcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\Captcha\Figlet as FigletCaptchaHelper;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

/**
 * @property FigletCaptchaHelper $helper
 */
class FigletTest extends AbstractCommonTestCase
{
    /** @var FigletCaptcha */
    private $captcha;

    protected function setUp(): void
    {
        $this->helper  = new FigletCaptchaHelper();
        $this->captcha = new FigletCaptcha();
        parent::setUp();
    }

    public function getElement(): CaptchaElement
    {
        $element = new CaptchaElement('foo');
        $element->setCaptcha($this->captcha);
        return $element;
    }

    public function testMissingCaptchaAttributeThrowsDomainException(): void
    {
        $element = new CaptchaElement('foo');

        $this->expectException(DomainException::class);
        $this->helper->render($element);
    }

    public function testRendersHiddenInputForId(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression(
            '#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(type="hidden")#',
            $markup
        );
        $this->assertMatchesRegularExpression(
            '#(name="' . $element->getName() . '\&\#x5B\;id\&\#x5D\;").*?(value="' . $this->captcha->getId() . '")#',
            $markup
        );
    }

    public function testRendersTextInputForInput(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression(
            '#(name="' . $element->getName() . '\&\#x5B\;input\&\#x5D\;").*?(type="text")#',
            $markup
        );
    }

    public function testRendersFigletPriorToInputByDefault(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString(
            '<pre>' . $this->captcha->getFiglet()->render($this->captcha->getWord()) . '</pre>'
            . $this->helper->getSeparator() . '<input',
            $markup
        );
    }

    public function testCanRenderFigletFollowingInput(): void
    {
        $this->helper->setCaptchaPosition('prepend');
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('><pre>', $markup);
    }
}
