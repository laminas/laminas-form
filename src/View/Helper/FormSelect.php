<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Stdlib\ArrayUtils;

use function array_key_exists;
use function array_map;
use function array_merge;
use function implode;
use function is_array;
use function is_scalar;
use function method_exists;
use function sprintf;

class FormSelect extends AbstractHelper
{
    /**
     * Attributes valid for select
     *
     * @var array
     */
    protected $validSelectAttributes = [
        'name'         => true,
        'autocomplete' => true,
        'autofocus'    => true,
        'disabled'     => true,
        'form'         => true,
        'multiple'     => true,
        'required'     => true,
        'size'         => true,
    ];

    /**
     * Attributes valid for options
     *
     * @var array
     */
    protected $validOptionAttributes = [
        'disabled' => true,
        'selected' => true,
        'label'    => true,
        'value'    => true,
    ];

    /**
     * Attributes valid for option groups
     *
     * @var array
     */
    protected $validOptgroupAttributes = [
        'disabled' => true,
        'label'    => true,
    ];

    /** @var array<string, bool> */
    protected $translatableAttributes = [
        'label' => true,
    ];

    /** @var FormHidden|null */
    protected $formHiddenHelper;

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormSelect
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <select> element from the provided $element
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        if (! $element instanceof SelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Select',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $options = $element->getValueOptions();

        if (($emptyOption = $element->getEmptyOption()) !== null) {
            $options = ['' => $emptyOption] + $options;
        }

        $attributes = $element->getAttributes();
        $value      = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;
        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }
        $this->validTagAttributes = $this->validSelectAttributes;

        $rendered = sprintf(
            '<select %s>%s</select>',
            $this->createAttributesString($attributes),
            $this->renderOptions($options, $value)
        );

        // Render hidden element
        if ($element->useHiddenElement()) {
            $rendered = $this->renderHiddenElement($element) . $rendered;
        }

        return $rendered;
    }

    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param  array $options
     * @param  array $selectedOptions Option values that should be marked as selected
     */
    public function renderOptions(array $options, array $selectedOptions = []): string
    {
        $template      = '<option %s>%s</option>';
        $optionStrings = [];
        $escapeHtml    = $this->getEscapeHtmlHelper();

        $stringSelectedOptions = array_map('strval', $selectedOptions);

        foreach ($options as $key => $optionSpec) {
            $value    = '';
            $label    = '';
            $selected = false;
            $disabled = false;

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key,
                ];
            }

            if (isset($optionSpec['options']) && is_array($optionSpec['options'])) {
                $optionStrings[] = $this->renderOptgroup($optionSpec, $selectedOptions);
                continue;
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

            if (ArrayUtils::inArray((string) $value, $stringSelectedOptions, true)) {
                $selected = true;
            }

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            $attributes = [
                'value'    => $value,
                'selected' => $selected,
                'disabled' => $disabled,
            ];

            if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
                $attributes = array_merge($attributes, $optionSpec['attributes']);
            }

            $this->validTagAttributes = $this->validOptionAttributes;
            $optionStrings[]          = sprintf(
                $template,
                $this->createAttributesString($attributes),
                $escapeHtml($label)
            );
        }

        return implode("\n", $optionStrings);
    }

    /**
     * Render an optgroup
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @param  array $optgroup
     * @param  array $selectedOptions
     */
    public function renderOptgroup(array $optgroup, array $selectedOptions = []): string
    {
        $template = '<optgroup%s>%s</optgroup>';

        $options = [];
        if (isset($optgroup['options']) && is_array($optgroup['options'])) {
            $options = $optgroup['options'];
            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes               = $this->createAttributesString($optgroup);
        if (! empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        return sprintf(
            $template,
            $attributes,
            $this->renderOptions($options, $selectedOptions)
        );
    }

    /**
     * Ensure that the value is set appropriately
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param  array $attributes
     * @return array
     * @throws Exception\DomainException
     */
    protected function validateMultiValue(mixed $value, array $attributes): array
    {
        if (null === $value) {
            return [];
        }

        if (! is_array($value)) {
            return [$value];
        }

        if (! isset($attributes['multiple']) || ! $attributes['multiple']) {
            throw new Exception\DomainException(sprintf(
                '%s does not allow specifying multiple selected values when the element does not have a multiple '
                . 'attribute set to a boolean true',
                self::class
            ));
        }

        return $value;
    }

    protected function renderHiddenElement(SelectElement $element): string
    {
        $hiddenElement = new Hidden($element->getName());
        $hiddenElement->setValue($element->getUnselectedValue());

        return $this->getFormHiddenHelper()->__invoke($hiddenElement);
    }

    protected function getFormHiddenHelper(): FormHidden
    {
        if (! $this->formHiddenHelper) {
            if (method_exists($this->view, 'plugin')) {
                $this->formHiddenHelper = $this->view->plugin('formhidden');
            }

            if (! $this->formHiddenHelper instanceof FormHidden) {
                $this->formHiddenHelper = new FormHidden();
            }
        }

        return $this->formHiddenHelper;
    }
}
