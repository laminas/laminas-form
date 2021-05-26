<?php

namespace Laminas\Form\View\Helper;

use Laminas\Captcha\AdapterInterface as CaptchaAdapter;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Exception;

use function method_exists;
use function sprintf;

class FormCaptcha extends AbstractHelper
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return string|FormCaptcha
     */
    public function __invoke(?Captcha $element = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form captcha for an element
     *
     * @throws Exception\DomainException If the element does not compose a captcha, or the renderer does
     *                                   not implement plugin().
     * @return string
     */
    public function render(Captcha $element)
    {
        $captcha = $element->getCaptcha();

        if ($captcha === null || ! $captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Laminas\Captcha\AdapterInterface; '
                . 'none found',
                __METHOD__
            ));
        }

        $helper = $captcha->getHelperName();

        $renderer = $this->getView();
        if (! method_exists($renderer, 'plugin')) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the renderer implements plugin(); it does not',
                __METHOD__
            ));
        }

        $helper = $renderer->plugin($helper);
        return $helper($element);
    }
}
