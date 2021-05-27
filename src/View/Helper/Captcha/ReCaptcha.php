<?php

namespace Laminas\Form\View\Helper\Captcha;

use Laminas\Captcha\ReCaptcha as CaptchaAdapter;
use Laminas\Form\Element\Captcha;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\View\Helper\FormInput;

use function assert;
use function sprintf;

class ReCaptcha extends FormInput
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return string|self
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render ReCaptcha form elements
     *
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element): string
    {
        assert($element instanceof Captcha);
        $captcha = $element->getCaptcha();

        if ($captcha === null || ! $captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Laminas\Captcha\AdapterInterface; '
                . 'none found',
                __METHOD__
            ));
        }

        $name = $element->getName();

        $markup = $captcha->getService()->getHtml();
        $hidden = $this->renderHiddenInput($name);

        return $hidden . $markup;
    }

    /**
     * Render hidden input element if the element's name is not 'g-recaptcha-response'
     * so that required validation works
     *
     * Note that only the first parameter is needed, the other three parameters
     * are deprecated.
     *
     * @param  string $name
     * @param  string $challengeId @deprecated
     * @param  string $responseName @deprecated
     * @param  string $responseId @deprecated
     * @return string
     */
    protected function renderHiddenInput($name, $challengeId = '', $responseName = '', $responseId = ''): string
    {
        if ($name === 'g-recaptcha-response') {
            return '';
        }

        $pattern        = '<input type="hidden" %s%s';
        $closingBracket = $this->getInlineClosingBracket();

        $attributes = $this->createAttributesString([
            'name'  => $name,
            'value' => 'g-recaptcha-response',
        ]);
        return sprintf($pattern, $attributes, $closingBracket);
    }

    /**
     * No longer used with v2 of Recaptcha API
     *
     * @deprecated
     *
     * @param  string $challengeId
     * @param  string $responseId
     * @return string
     */
    protected function renderJsEvents($challengeId, $responseId): string
    {
        return '';
    }
}
