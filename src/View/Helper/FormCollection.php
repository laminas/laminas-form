<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element\Collection as CollectionElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\HelperInterface;
use RuntimeException;

use function assert;
use function is_callable;
use function method_exists;
use function sprintf;

class FormCollection extends AbstractHelper
{
    /**
     * Attributes valid for this tag (form)
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name' => true,
    ];

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var bool
     */
    protected $shouldWrap = true;

    /**
     * This is the default wrapper that the collection is wrapped into
     *
     * @var string
     */
    protected $wrapper = '<fieldset%4$s>%2$s%1$s%3$s</fieldset>';

    /**
     * This is the default label-wrapper
     *
     * @var string
     */
    protected $labelWrapper = '<legend>%s</legend>';

    /**
     * Where shall the template-data be inserted into
     *
     * @var string
     */
    protected $templateWrapper = '<span data-template="%s"></span>';

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = 'formrow';

    /**
     * The view helper used to render sub elements.
     *
     * @var null|HelperInterface
     */
    protected $elementHelper;

    /**
     * The view helper used to render sub fieldsets.
     *
     * @var null|HelperInterface
     */
    protected $fieldsetHelper;

    private array $doctypesAllowedToHaveNameAttribute = [
        Doctype::HTML5  => true,
        Doctype::XHTML5 => true,
    ];

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormCollection
     */
    public function __invoke(?ElementInterface $element = null, bool $wrap = true)
    {
        if (! $element) {
            return $this;
        }

        $this->setShouldWrap($wrap);

        return $this->render($element);
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     */
    public function render(ElementInterface $element): string
    {
        $renderer = $this->getView();
        if ($renderer !== null && ! method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $markup         = '';
        $templateMarkup = '';
        $elementHelper  = $this->getElementHelper();
        assert(is_callable($elementHelper));
        $fieldsetHelper = $this->getFieldsetHelper();
        assert(is_callable($fieldsetHelper));

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $fieldsetHelper($elementOrFieldset, $this->shouldWrap());
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $attributes = $element->getAttributes();
            if (! isset($this->doctypesAllowedToHaveNameAttribute[$this->getDoctype()])) {
                unset($attributes['name']);
            }
            $attributesString = $attributes !== [] ? ' ' . $this->createAttributesString($attributes) : '';

            $label  = $element->getLabel();
            $legend = '';

            if (! empty($label)) {
                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate(
                        $label,
                        $this->getTranslatorTextDomain()
                    );
                }

                if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                    $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                    $label            = $escapeHtmlHelper($label);
                }

                $legend = sprintf(
                    $this->labelWrapper,
                    $label
                );
            }

            $markup = sprintf(
                $this->wrapper,
                $markup,
                $legend,
                $templateMarkup,
                $attributesString
            );
        } else {
            $markup .= $templateMarkup;
        }

        return $markup;
    }

    /**
     * Only render a template
     */
    public function renderTemplate(CollectionElement $collection): string
    {
        $elementHelper = $this->getElementHelper();
        assert(is_callable($elementHelper));
        $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();
        $fieldsetHelper         = $this->getFieldsetHelper();
        assert(is_callable($fieldsetHelper));

        $templateMarkup = '';

        $elementOrFieldset = $collection->getTemplateElement();

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $templateMarkup .= $fieldsetHelper($elementOrFieldset, $this->shouldWrap());
        } elseif ($elementOrFieldset instanceof ElementInterface) {
            $templateMarkup .= $elementHelper($elementOrFieldset);
        }

        return sprintf(
            $this->getTemplateWrapper(),
            $escapeHtmlAttribHelper($templateMarkup)
        );
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @return $this
     */
    public function setShouldWrap(bool $wrap)
    {
        $this->shouldWrap = $wrap;
        return $this;
    }

    /**
     * Get wrapped
     */
    public function shouldWrap(): bool
    {
        return $this->shouldWrap;
    }

    /**
     * Sets the name of the view helper that should be used to render sub elements.
     *
     * @param  string $defaultSubHelper The name of the view helper to set.
     * @return $this
     */
    public function setDefaultElementHelper(string $defaultSubHelper)
    {
        $this->defaultElementHelper = $defaultSubHelper;
        return $this;
    }

    /**
     * Gets the name of the view helper that should be used to render sub elements.
     */
    public function getDefaultElementHelper(): string
    {
        return $this->defaultElementHelper;
    }

    /**
     * Sets the element helper that should be used by this collection.
     *
     * @param  HelperInterface $elementHelper The element helper to use.
     * @return $this
     */
    public function setElementHelper(HelperInterface $elementHelper)
    {
        $this->elementHelper = $elementHelper;
        return $this;
    }

    /**
     * Retrieve the element helper.
     *
     * @throws RuntimeException
     */
    protected function getElementHelper(): HelperInterface
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin($this->getDefaultElementHelper());
        }

        if (! $this->elementHelper instanceof HelperInterface) {
            throw new RuntimeException(
                'Invalid element helper set in FormCollection. The helper must be an '
                . 'instance of Laminas\View\Helper\HelperInterface.'
            );
        }

        return $this->elementHelper;
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

        return $this;
    }

    /**
     * Get the wrapper for the collection
     */
    public function getWrapper(): string
    {
        return $this->wrapper;
    }

    /**
     * Set the wrapper for this collection
     *
     * The string given will be passed through sprintf with the following three
     * replacements:
     *
     * 1. The content of the collection
     * 2. The label of the collection. If no label is given this will be an empty
     *   string
     * 3. The template span-tag. This might also be an empty string
     *
     * The preset default is <pre><fieldset>%2$s%1$s%3$s</fieldset></pre>
     *
     * @return $this
     */
    public function setWrapper(string $wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * Set the label-wrapper
     * The string will be passed through sprintf with the label as single
     * parameter
     * This defaults to '<legend>%s</legend>'
     *
     * @return $this
     */
    public function setLabelWrapper(string $labelWrapper)
    {
        $this->labelWrapper = $labelWrapper;

        return $this;
    }

    /**
     * Get the wrapper for the label
     */
    public function getLabelWrapper(): string
    {
        return $this->labelWrapper;
    }

    /**
     * Ge the wrapper for the template
     */
    public function getTemplateWrapper(): string
    {
        return $this->templateWrapper;
    }

    /**
     * Set the string where the template will be inserted into
     *
     * This string will be passed through sprintf and has the template as single
     * parameter
     *
     * THis defaults to '<span data-template="%s"></span>'
     *
     * @return $this
     */
    public function setTemplateWrapper(string $templateWrapper)
    {
        $this->templateWrapper = $templateWrapper;

        return $this;
    }
}
