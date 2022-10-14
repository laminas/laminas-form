<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper\Captcha;

use Laminas\Captcha\ReCaptcha;
use Laminas\Form\Element\Captcha as CaptchaElement;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\Captcha\ReCaptcha as ReCaptchaHelper;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

use function class_exists;
use function getenv;

/**
 * @property ReCaptchaHelper $helper
 */
final class ReCaptchaTest extends AbstractCommonTestCase
{
    private ReCaptcha $captcha;

    protected function setUp(): void
    {
        if (! class_exists(ReCaptcha::class)) {
            $this->markTestSkipped(
                'laminas-captcha-related tests are skipped until the component '
                . 'is forwards-compatible with laminas-servicemanager v3'
            );
        }

        $this->helper  = new ReCaptchaHelper();
        $this->captcha = new ReCaptcha();
        $this->captcha->setSiteKey((string) getenv('TESTS_LAMINAS_FORM_RECAPTCHA_PUBLIC_KEY'));
        $this->captcha->setSecretKey((string) getenv('TESTS_LAMINAS_FORM_RECAPTCHA_PRIVATE_KEY'));
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

    public function testRendersHiddenInputWhenNameIsNotRecaptchaDefault(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('value="g-recaptcha-response"', $markup);
    }

    public function testDoesNotRenderHiddenInputWhenNameIsRecaptchaDefault(): void
    {
        $element = $this->getElement();
        $element->setName('g-recaptcha-response');
        $markup = $this->helper->render($element);
        $this->assertStringNotContainsString('type="hidden"', $markup);
    }

    public function testRendersReCaptchaMarkup(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString($this->captcha->getService()->getHtml(), $markup);
    }
}
