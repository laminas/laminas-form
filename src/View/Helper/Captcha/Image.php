<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper\Captcha;

use Laminas\Captcha\Image as CaptchaAdapter;
use Laminas\Form\Element\Captcha;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function assert;
use function sprintf;

class Image extends AbstractWord
{
    /**
     * Render the captcha
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        assert($element instanceof Captcha);
        $captcha = $element->getCaptcha();

        if ($captcha === null || ! $captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute of type Laminas\Captcha\Image; none found',
                __METHOD__
            ));
        }

        $captcha->generate();

        $imgAttributes = [
            'width'  => $captcha->getWidth(),
            'height' => $captcha->getHeight(),
            'alt'    => $captcha->getImgAlt(),
            'src'    => $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix(),
        ];

        if ($element->hasAttribute('id')) {
            $imgAttributes['id'] = $element->getAttribute('id') . '-image';
        }

        $closingBracket = $this->getInlineClosingBracket();
        $img            = sprintf(
            '<img %s%s',
            $this->createAttributesString($imgAttributes),
            $closingBracket
        );

        $position     = $this->getCaptchaPosition();
        $separator    = $this->getSeparator();
        $captchaInput = $this->renderCaptchaInputs($element);

        $pattern = '%s%s%s';
        if ($position === self::CAPTCHA_PREPEND) {
            return sprintf($pattern, $captchaInput, $separator, $img);
        }

        return sprintf($pattern, $img, $separator, $captchaInput);
    }
}
