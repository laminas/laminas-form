<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;

use function array_merge;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

class FormLabel extends AbstractHelper
{
    public const APPEND  = 'append';
    public const PREPEND = 'prepend';

    /**
     * Attributes valid for the label tag
     *
     * @var array
     */
    protected $validTagAttributes = [
        'for'  => true,
        'form' => true,
    ];

    /**
     * Generate a form label, optionally with content
     *
     * Always generates a "for" statement, as we cannot assume the form input
     * will be provided in the $labelContent.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @throws Exception\DomainException
     * @return string|FormLabel
     */
    public function __invoke(?ElementInterface $element = null, ?string $labelContent = null, ?string $position = null)
    {
        if (! $element) {
            return $this;
        }

        $openTag = $this->openTag($element);
        $label   = '';
        if ($labelContent === null || $position !== null) {
            $label = $element->getLabel();
            if (empty($label)) {
                throw new Exception\DomainException(
                    sprintf(
                        '%s expects either label content as the second argument, '
                        . 'or that the element provided has a label attribute; neither found',
                        __METHOD__
                    )
                );
            }

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                $label            = $escapeHtmlHelper($label);
            }
        }

        if ($label && $labelContent) {
            switch ($position) {
                case self::APPEND:
                    $labelContent .= $label;
                    break;
                case self::PREPEND:
                default:
                    $labelContent = $label . $labelContent;
                    break;
            }
        }

        if ($label && null === $labelContent) {
            $labelContent = $label;
        }

        return $openTag . $labelContent . $this->closeTag();
    }

    /**
     * Generate an opening label tag
     *
     * @param  null|array|ElementInterface $attributesOrElement
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function openTag($attributesOrElement = null): string
    {
        if (null === $attributesOrElement || [] === $attributesOrElement) {
            return '<label>';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);
            return sprintf('<label %s>', $attributes);
        }

        if (! $attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Laminas\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement)
            ));
        }

        $id = $this->getId($attributesOrElement);
        if (null === $id) {
            throw new Exception\DomainException(sprintf(
                '%s expects the Element provided to have either a name or an id present; neither found',
                __METHOD__
            ));
        }

        $labelAttributes = [];
        if ($attributesOrElement instanceof LabelAwareInterface) {
            $labelAttributes = $attributesOrElement->getLabelAttributes();
        }

        $attributes = ['for' => $id];

        if (! empty($labelAttributes)) {
            $attributes = array_merge($labelAttributes, $attributes);
        }

        $attributes = $this->createAttributesString($attributes);
        return sprintf('<label %s>', $attributes);
    }

    /**
     * Return a closing label tag
     */
    public function closeTag(): string
    {
        return '</label>';
    }
}
