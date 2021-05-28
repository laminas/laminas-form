<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;

use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;
use function strtolower;

class FormButton extends FormInput
{
    /**
     * Attributes valid for the button tag
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'           => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'type'           => true,
        'value'          => true,
    ];

    /**
     * Valid values for the button type
     *
     * @var array
     */
    protected $validTypes = [
        'button' => true,
        'reset'  => true,
        'submit' => true,
    ];

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormButton
     */
    public function __invoke(?ElementInterface $element = null, ?string $buttonContent = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element, $buttonContent);
    }

    /**
     * Render a form <button> element from the provided $element,
     * using content from $buttonContent or the element's "label" attribute
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element, ?string $buttonContent = null): string
    {
        $openTag = $this->openTag($element);

        if (null === $buttonContent) {
            $buttonContent = $element->getLabel();
            if (null === $buttonContent) {
                throw new Exception\DomainException(
                    sprintf(
                        '%s expects either button content as the second argument, '
                        . 'or that the element provided has a label value; neither found',
                        __METHOD__
                    )
                );
            }
        }

        if (null !== ($translator = $this->getTranslator())) {
            $buttonContent = $translator->translate(
                $buttonContent,
                $this->getTranslatorTextDomain()
            );
        }

        if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
            $escapeHtmlHelper = $this->getEscapeHtmlHelper();
            $buttonContent    = $escapeHtmlHelper($buttonContent);
        }

        return $openTag . $buttonContent . $this->closeTag();
    }

    /**
     * Generate an opening button tag
     *
     * @param  null|array|ElementInterface $attributesOrElement
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function openTag($attributesOrElement = null): string
    {
        if (null === $attributesOrElement) {
            return '<button>';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);
            return sprintf('<button %s>', $attributes);
        }

        if (! $attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Laminas\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement)
            ));
        }

        $element = $attributesOrElement;
        $name    = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $attributes['type']  = $this->getType($element);
        $attributes['value'] = $element->getValue();

        return sprintf(
            '<button %s>',
            $this->createAttributesString($attributes)
        );
    }

    /**
     * Return a closing button tag
     */
    public function closeTag(): string
    {
        return '</button>';
    }

    /**
     * Determine button type to use
     */
    protected function getType(ElementInterface $element): string
    {
        $type = $element->getAttribute('type');
        if (empty($type)) {
            return 'submit';
        }

        $type = strtolower($type);
        if (! isset($this->validTypes[$type])) {
            return 'submit';
        }

        return $type;
    }
}
