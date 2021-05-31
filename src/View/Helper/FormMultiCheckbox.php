<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;

use function array_key_exists;
use function array_merge;
use function implode;
use function in_array;
use function is_scalar;
use function method_exists;
use function sprintf;
use function strtolower;

class FormMultiCheckbox extends FormInput
{
    public const LABEL_APPEND  = 'append';
    public const LABEL_PREPEND = 'prepend';

    /**
     * The attributes applied to option label
     *
     * @var null|array
     */
    protected $labelAttributes;

    /**
     * Where will be label rendered?
     *
     * @var string
     */
    protected $labelPosition = self::LABEL_APPEND;

    /**
     * Separator for checkbox elements
     *
     * @var string
     */
    protected $separator = '';

    /**
     * Prefixing the element with a hidden element for the unset value?
     *
     * @var bool
     */
    protected $useHiddenElement = false;

    /**
     * The unchecked value used when "UseHiddenElement" is turned on
     *
     * @var string
     */
    protected $uncheckedValue = '';

    /**
     * Form input helper instance
     *
     * @var null|FormInput
     */
    protected $inputHelper;

    /**
     * Form label helper instance
     *
     * @var null|FormLabel
     */
    protected $labelHelper;

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormMultiCheckbox
     */
    public function __invoke(?ElementInterface $element = null, ?string $labelPosition = null)
    {
        if (! $element) {
            return $this;
        }

        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        }

        return $this->render($element);
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     */
    public function render(ElementInterface $element): string
    {
        if (! $element instanceof MultiCheckboxElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\MultiCheckbox',
                __METHOD__
            ));
        }

        $name = static::getName($element);

        $options = $element->getValueOptions();

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $selectedOptions    = (array) $element->getValue();

        $rendered = $this->renderOptions($element, $options, $selectedOptions, $attributes);

        // Render hidden element
        if ($element->useHiddenElement()) {
            $rendered = $this->renderHiddenElement($element) . $rendered;
        }

        return $rendered;
    }

    /**
     * Render options
     *
     * @param  array                $options
     * @param  array                $selectedOptions
     * @param  array                $attributes
     */
    protected function renderOptions(
        MultiCheckboxElement $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ): string {
        $escapeHtmlHelper      = $this->getEscapeHtmlHelper();
        $labelHelper           = $this->getLabelHelper();
        $labelClose            = $labelHelper->closeTag();
        $labelPosition         = $this->getLabelPosition();
        $globalLabelAttributes = [];
        $closingBracket        = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count          = 0;

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected        = isset($inputAttributes['selected'])
                && $inputAttributes['type'] !== 'radio'
                && $inputAttributes['selected'];
            $disabled        = isset($inputAttributes['disabled']) && $inputAttributes['disabled'];

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key,
                ];
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }
            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = isset($labelAttributes)
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }
            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);
            $template  = $labelOpen . '%s%s' . $labelClose;
            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = sprintf($template, $label, $input);
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = sprintf($template, $input, $label);
                    break;
            }

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }

    /**
     * Render a hidden element for empty/unchecked value
     */
    protected function renderHiddenElement(MultiCheckboxElement $element): string
    {
        $closingBracket = $this->getInlineClosingBracket();

        $uncheckedValue = $element->getUncheckedValue() ?: $this->uncheckedValue;

        $hiddenAttributes = [
            'name'  => $element->getName(),
            'value' => $uncheckedValue,
        ];

        return sprintf(
            '<input type="hidden" %s%s',
            $this->createAttributesString($hiddenAttributes),
            $closingBracket
        );
    }

    /**
     * Sets the attributes applied to option label.
     *
     * @param  array|null $attributes
     * @return $this
     */
    public function setLabelAttributes(?array $attributes)
    {
        $this->labelAttributes = $attributes;
        return $this;
    }

    /**
     * Returns the attributes applied to each option label.
     *
     * @return array|null
     */
    public function getLabelAttributes(): ?array
    {
        return $this->labelAttributes;
    }

    /**
     * Set value for labelPosition
     *
     * @throws Exception\InvalidArgumentException
     * @return $this
     */
    public function setLabelPosition(string $labelPosition)
    {
        $labelPosition = strtolower($labelPosition);
        if (! in_array($labelPosition, [self::LABEL_APPEND, self::LABEL_PREPEND])) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                __METHOD__,
                self::class,
                self::class,
                $labelPosition
            ));
        }
        $this->labelPosition = $labelPosition;

        return $this;
    }

    /**
     * Get position of label
     */
    public function getLabelPosition(): string
    {
        return $this->labelPosition;
    }

    /**
     * Set separator string for checkbox elements
     *
     * @return $this
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Get separator for checkbox elements
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Sets the option for prefixing the element with a hidden element
     * for the unset value.
     *
     * @return $this
     */
    public function setUseHiddenElement(bool $useHiddenElement)
    {
        $this->useHiddenElement = $useHiddenElement;
        return $this;
    }

    /**
     * Returns the option for prefixing the element with a hidden element
     * for the unset value.
     */
    public function getUseHiddenElement(): bool
    {
        return $this->useHiddenElement;
    }

    /**
     * Sets the unchecked value used when "UseHiddenElement" is turned on.
     *
     * @return $this
     */
    public function setUncheckedValue(string $value)
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    /**
     * Returns the unchecked value used when "UseHiddenElement" is turned on.
     */
    public function getUncheckedValue(): string
    {
        return $this->uncheckedValue;
    }

    /**
     * Return input type
     */
    protected function getInputType(): string
    {
        return 'checkbox';
    }

    /**
     * Get element name
     *
     * @throws Exception\DomainException
     */
    protected static function getName(ElementInterface $element): string
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }
        return $name . '[]';
    }

    /**
     * Retrieve the FormInput helper
     */
    protected function getInputHelper(): FormInput
    {
        if (null !== $this->inputHelper) {
            return $this->inputHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->inputHelper = $this->view->plugin('form_input');
        }

        if (! $this->inputHelper instanceof FormInput) {
            $this->inputHelper = new FormInput();
        }

        return $this->inputHelper;
    }

    /**
     * Retrieve the FormLabel helper
     */
    protected function getLabelHelper(): FormLabel
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (! $this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
        }

        return $this->labelHelper;
    }
}
