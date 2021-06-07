<?php

declare(strict_types=1);

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
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
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
        assert($name !== null);

        $markup = $captcha->getService()->getHtml();
        $hidden = $this->renderHiddenInput($name);

        return $hidden . $markup;
    }

    /**
     * Render hidden input element if the element's name is not 'g-recaptcha-response'
     * so that required validation works
     */
    protected function renderHiddenInput(string $name): string
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
}
