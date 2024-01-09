<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

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
     * @template T as null|Captcha
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
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
     */
    public function render(Captcha $element): string
    {
        $captcha = $element->getCaptcha();

        if ($captcha === null) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Laminas\Captcha\AdapterInterface; '
                . 'none found',
                __METHOD__
            ));
        }

        $helper = $captcha->getHelperName();

        $renderer = $this->getView();
        if ($renderer === null || ! method_exists($renderer, 'plugin')) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the renderer implements plugin(); it does not',
                __METHOD__
            ));
        }

        $helper = $renderer->plugin($helper);
        return $helper($element);
    }
}
