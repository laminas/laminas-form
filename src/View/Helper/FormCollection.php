<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Element\Collection as CollectionElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;
use Laminas\View\Helper\AbstractHelper as BaseAbstractHelper;
use RuntimeException;

class FormCollection extends AbstractHelper
{
    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var bool
     */
    protected $shouldWrap = true;

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = 'formrow';

    /**
     * The view helper used to render sub elements.
     *
     * @var AbstractHelper
     */
    protected $elementHelper;

    /**
     * The view helper used to render sub fieldsets.
     *
     * @var AbstractHelper
     */
    protected $fieldsetHelper;

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @param  bool                  $wrap
     * @return string|FormCollection
     */
    public function __invoke(ElementInterface $element = null, $wrap = true)
    {
        if (!$element) {
            return $this;
        }

        $this->setShouldWrap($wrap);

        return $this->render($element);
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $attributes       = $element->getAttributes();
        $markup           = '';
        $templateMarkup   = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper    = $this->getElementHelper();
        $fieldsetHelper   = $this->getFieldsetHelper();

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $fieldsetHelper($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $markup .= $templateMarkup;
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $label = $element->getLabel();
            $legend = '';

            if (!empty($label)) {

                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate(
                        $label,
                        $this->getTranslatorTextDomain()
                    );
                }

                $label = $escapeHtmlHelper($label);

                $legend = sprintf(
                    '<legend>%s</legend>',
                    $label
                );
            }

            $attributesString = $this->createAttributesString($attributes);
            if (!empty($attributesString)) {
                $attributesString = ' ' . $attributesString;
            }

            $markup = sprintf(
                '<fieldset%s>%s%s</fieldset>',
                $attributesString,
                $legend,
                $markup
            );
        }

        return $markup;
    }

    /**
     * Only render a template
     *
     * @param  CollectionElement $collection
     * @return string
     */
    public function renderTemplate(CollectionElement $collection)
    {
        $elementHelper          = $this->getElementHelper();
        $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();
        $templateMarkup         = '';

        $elementOrFieldset = $collection->getTemplateElement();

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $templateMarkup .= $this->render($elementOrFieldset);
        } elseif ($elementOrFieldset instanceof ElementInterface) {
            $templateMarkup .= $elementHelper($elementOrFieldset);
        }

        return sprintf(
            '<span data-template="%s"></span>',
            $escapeHtmlAttribHelper($templateMarkup)
        );
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @param  bool $wrap
     * @return FormCollection
     */
    public function setShouldWrap($wrap)
    {
        $this->shouldWrap = (bool) $wrap;
        return $this;
    }

    /**
     * Get wrapped
     *
     * @return bool
     */
    public function shouldWrap()
    {
        return $this->shouldWrap;
    }

    /**
     * Sets the name of the view helper that should be used to render sub elements.
     *
     * @param  string $defaultSubHelper The name of the view helper to set.
     * @return FormCollection
     */
    public function setDefaultElementHelper($defaultSubHelper)
    {
        $this->defaultElementHelper = $defaultSubHelper;
        return $this;
    }

    /**
     * Gets the name of the view helper that should be used to render sub elements.
     *
     * @return string
     */
    public function getDefaultElementHelper()
    {
        return $this->defaultElementHelper;
    }

    /**
     * Sets the element helper that should be used by this collection.
     *
     * @param  AbstractHelper $elementHelper The element helper to use.
     * @return FormCollection
     */
    public function setElementHelper(AbstractHelper $elementHelper)
    {
        $this->elementHelper = $elementHelper;
        return $this;
    }

    /**
     * Retrieve the element helper.
     *
     * @return AbstractHelper
     * @throws RuntimeException
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin($this->getDefaultElementHelper());
        }

        if (!$this->elementHelper instanceof BaseAbstractHelper) {
            // @todo Ideally the helper should implement an interface.
            throw new RuntimeException('Invalid element helper set in FormCollection. The helper must be an instance of AbstractHelper.');
        }

        return $this->elementHelper;
    }

    /**
     * Sets the fieldset helper that should be used by this collection.
     *
     * @param  AbstractHelper $fieldsetHelper The fieldset helper to use.
     * @return FormCollection
     */
    public function setFieldsetHelper(AbstractHelper $fieldsetHelper)
    {
        $this->fieldsetHelper = $fieldsetHelper;
        return $this;
    }

    /**
     * Retrieve the fieldset helper.
     *
     * @return FormCollection
     */
    protected function getFieldsetHelper()
    {
        if ($this->fieldsetHelper) {
            return $this->fieldsetHelper;
        }

        return $this;
    }
}
