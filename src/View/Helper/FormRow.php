<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element\Button;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\MonthSelect;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\LabelAwareInterface;

use Laminas\View\Helper\HelperInterface;
use RuntimeException;
use function in_array;
use function is_array;
use function method_exists;
use function sprintf;
use function strtolower;

class FormRow extends AbstractHelper
{
    public const LABEL_APPEND  = 'append';
    public const LABEL_PREPEND = 'prepend';

    /**
     * The class that is added to element that have errors
     *
     * @var string
     */
    protected $inputErrorClass = 'input-error';

    /**
     * The attributes for the row label
     *
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * Where will be label rendered?
     *
     * @var string
     */
    protected $labelPosition = self::LABEL_PREPEND;

    /**
     * Are the errors are rendered by this helper?
     *
     * @var bool
     */
    protected $renderErrors = true;

    /**
     * Form label helper instance
     *
     * @var null|FormLabel
     */
    protected $labelHelper;

    /**
     * Form element helper instance
     *
     * @var null|FormElement
     */
    protected $elementHelper;

    /**
     * The view helper used to render sub fieldsets.
     *
     * @var null|HelperInterface
     */
    protected $fieldsetHelper;

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultFieldsetHelper = 'formCollection';

    /**
     * Form element errors helper instance
     *
     * @var null|FormElementErrors
     */
    protected $elementErrorsHelper;

    /** @var null|string */
    protected $partial;

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormRow
     */
    public function __invoke(
        ?ElementInterface $element = null,
        ?string $labelPosition = null,
        ?bool $renderErrors = null,
        ?string $partial = null
    ) {
        if (! $element) {
            return $this;
        }

        if ($labelPosition === null) {
            $labelPosition = $this->getLabelPosition();
        }

        if ($renderErrors !== null) {
            $this->setRenderErrors($renderErrors);
        }

        if ($partial !== null) {
            $this->setPartial($partial);
        }

        return $this->render($element, $labelPosition);
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element, ?string $labelPosition = null): string
    {
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label           = $element->getLabel() ?? '';
        $inputErrorClass = $this->getInputErrorClass();

        if ($labelPosition === null) {
            $labelPosition = $this->labelPosition;
        }

        if ('' !== $label) {
            // Translate the label
            $label = $this->translateLabel($label);
        }

        // Does this element have errors ?
        if ($element->getMessages() && $inputErrorClass) {
            $classAttributes  = $element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '';
            $classAttributes .= $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if ($this->partial) {
            $vars = [
                'element'         => $element,
                'label'           => $label,
                'labelAttributes' => $this->labelAttributes,
                'labelPosition'   => $labelPosition,
                'renderErrors'    => $this->renderErrors,
            ];

            return $this->view->render($this->partial, $vars);
        }

        $elementErrors = '';
        if ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element);
        }

        $elementString = $elementHelper->render($element);

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');

        if ($label === '' || $type === 'hidden') {
            return $elementString . $elementErrors;
        }

        $labelAttributes = [];

        if ($element instanceof LabelAwareInterface) {
            $labelAttributes = $element->getLabelAttributes();
        }

        $label = $this->escapeLabel($element, $label);

        if (empty($labelAttributes)) {
            $labelAttributes = $this->labelAttributes;
        }

        // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
        // labels. The semantic way is to group them inside a fieldset
        if (
            $type === 'multi_checkbox'
            || $type === 'radio'
            || $element instanceof MonthSelect
            || $element instanceof Captcha
        ) {
            //            $fieldsetHelper = $this->getFieldsetHelper();
//            assert(is_callable($fieldsetHelper));
//
//            $collection = new Collection();
//            $collection->add($element);
//
//            $markup = $fieldsetHelper($collection);

            $legendAttributesData    = $element->getOption('legend_attributes');
            $wrapperAttributesData   = $element->getOption('wrapper_attributes');
            $wrapperAttributesString = '';
            $legendAttributesString  = '';

            if (is_array($legendAttributesData) && $legendAttributesData !== []) {
                $legendAttributesString = ' ' . (new FormLabel())->createAttributesString($legendAttributesData);
            }

            if (is_array($wrapperAttributesData) && $wrapperAttributesData !== []) {
                $wrapperAttributesString = ' '
                    . (new FormCollection())->createAttributesString($wrapperAttributesData);
            }

            $markup = sprintf(
                '<fieldset%s><legend%s>%s</legend>%s</fieldset>',
                $wrapperAttributesString,
                $legendAttributesString,
                $label,
                $elementString
            );

            return $markup . $elementErrors;
        }

        // Ensure element and label will be separated if element has an `id`-attribute.
        // If element has label option `always_wrap` it will be nested in any case.
        if (
            $element->hasAttribute('id')
            && ($element instanceof LabelAwareInterface && ! $element->getLabelOption('always_wrap'))
        ) {
            $labelOpen  = '';
            $labelClose = '';
            $label      = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
        } else {
            $labelOpen  = $labelHelper->openTag($labelAttributes);
            $labelClose = $labelHelper->closeTag();
        }

        if (
            $label !== '' && (! $element->hasAttribute('id'))
            || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
        ) {
            $label = '<span>' . $label . '</span>';
        }

        // Button element is a special case, because label is always rendered inside it
        if ($element instanceof Button) {
            $labelOpen = $labelClose = $label = '';
        }

        if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position')) {
            $labelPosition = $element->getLabelOption('label_position');
        }

        $markup = match ($labelPosition) {
            self::LABEL_PREPEND => $labelOpen . $label . $elementString . $labelClose,
            default => $labelOpen . $elementString . $label . $labelClose,
        };

        return $markup . $elementErrors;
    }

    /**
     * Set the class that is added to element that have errors
     *
     * @return $this
     */
    public function setInputErrorClass(string $inputErrorClass)
    {
        $this->inputErrorClass = $inputErrorClass;
        return $this;
    }

    /**
     * Get the class that is added to element that have errors
     */
    public function getInputErrorClass(): string
    {
        return $this->inputErrorClass;
    }

    /**
     * Set the attributes for the row label
     *
     * @return $this
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the attributes for the row label
     *
     * @return array
     */
    public function getLabelAttributes(): array
    {
        return $this->labelAttributes;
    }

    /**
     * Set the label position
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
     * Get the label position
     */
    public function getLabelPosition(): string
    {
        return $this->labelPosition;
    }

    /**
     * Set if the errors are rendered by this helper
     *
     * @return $this
     */
    public function setRenderErrors(bool $renderErrors)
    {
        $this->renderErrors = $renderErrors;
        return $this;
    }

    /**
     * Retrieve if the errors are rendered by this helper
     */
    public function getRenderErrors(): bool
    {
        return $this->renderErrors;
    }

    /**
     * Set a partial view script to use for rendering the row
     *
     * @return $this
     */
    public function setPartial(?string $partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * Retrieve current partial
     */
    public function getPartial(): ?string
    {
        return $this->partial;
    }

    /**
     * Retrieve the FormLabel helper
     */
    protected function getLabelHelper(): FormLabel
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (! $this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
        }

        if ($this->hasTranslator()) {
            $this->labelHelper->setTranslator(
                $this->getTranslator(),
                $this->getTranslatorTextDomain()
            );
        }

        return $this->labelHelper;
    }

    /**
     * Retrieve the FormElement helper
     */
    protected function getElementHelper(): FormElement
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (! $this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }

    /**
     * Retrieve the FormElementErrors helper
     */
    protected function getElementErrorsHelper(): FormElementErrors
    {
        if ($this->elementErrorsHelper) {
            return $this->elementErrorsHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->elementErrorsHelper = $this->view->plugin('form_element_errors');
        }

        if (! $this->elementErrorsHelper instanceof FormElementErrors) {
            $this->elementErrorsHelper = new FormElementErrors();
        }

        return $this->elementErrorsHelper;
    }

    /**
     * Sets the name of the view helper that should be used to render sub elements.
     *
     * @param  string $defaultSubHelper The name of the view helper to set.
     * @return $this
     */
    public function setDefaultFieldsetHelper(string $defaultSubHelper)
    {
        $this->defaultFieldsetHelper = $defaultSubHelper;
        return $this;
    }

    /**
     * Gets the name of the view helper that should be used to render sub elements.
     */
    public function getDefaultFieldsetHelper(): string
    {
        return $this->defaultFieldsetHelper;
    }

    /**
     * Sets the fieldset helper that should be used by this collection.
     *
     * @param  HelperInterface $fieldsetHelper The fieldset helper to use.
     * @return $this
     */
    public function setFieldsetHelper(HelperInterface $fieldsetHelper)
    {
        $this->fieldsetHelper = $fieldsetHelper;
        return $this;
    }

    /**
     * Retrieve the fieldset helper.
     */
    protected function getFieldsetHelper(): HelperInterface
    {
        if ($this->fieldsetHelper) {
            return $this->fieldsetHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->fieldsetHelper = $this->view->plugin($this->getDefaultFieldsetHelper());
        }

        if (! $this->fieldsetHelper instanceof HelperInterface) {
            throw new RuntimeException(
                'Invalid element helper set in FormRow. The helper must be an '
                . 'instance of Laminas\View\Helper\HelperInterface.'
            );
        }

        return $this->fieldsetHelper;
    }
}
