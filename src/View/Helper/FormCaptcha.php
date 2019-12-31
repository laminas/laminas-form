<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\View\Helper;

use Laminas\Captcha\AdapterInterface as CaptchaAdapter;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage View
 */
class FormCaptcha extends AbstractHelper
{
    /**
     * Render a form captcha for an element
     *
     * @param  ElementInterface $element
     * @return string
     * @throws Exception\DomainException if the element does not compose a captcha, or the renderer does not implement plugin()
     */
    public function render(ElementInterface $element)
    {
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Laminas\Captcha\AdapterInterface; none found',
                __METHOD__
            ));
        }

        $helper  = $captcha->getHelperName();

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the renderer implements plugin(); it does not',
                __METHOD__
            ));
        }

        $helper = $renderer->plugin($helper);
        return $helper($element);
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface $element
     * @return string|FormCaptcha
     */
    public function __invoke(ElementInterface $element)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }
}
