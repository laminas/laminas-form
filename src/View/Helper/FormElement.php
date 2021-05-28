<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\View\Helper\AbstractHelper as BaseAbstractHelper;
use Laminas\View\Renderer\PhpRenderer;

use function assert;
use function is_callable;
use function method_exists;

class FormElement extends BaseAbstractHelper
{
    public const DEFAULT_HELPER = 'forminput';

    /**
     * Instance map to view helper
     *
     * @var array
     */
    protected $classMap = [
        Element\Button::class         => 'formbutton',
        Element\Captcha::class        => 'formcaptcha',
        Element\Csrf::class           => 'formhidden',
        Element\Collection::class     => 'formcollection',
        Element\DateTimeSelect::class => 'formdatetimeselect',
        Element\DateSelect::class     => 'formdateselect',
        Element\MonthSelect::class    => 'formmonthselect',
    ];

    /**
     * Type map to view helper
     *
     * @var array
     */
    protected $typeMap = [
        'checkbox'       => 'formcheckbox',
        'color'          => 'formcolor',
        'date'           => 'formdate',
        'datetime'       => 'formdatetime',
        'datetime-local' => 'formdatetimelocal',
        'email'          => 'formemail',
        'file'           => 'formfile',
        'hidden'         => 'formhidden',
        'image'          => 'formimage',
        'month'          => 'formmonth',
        'multi_checkbox' => 'formmulticheckbox',
        'number'         => 'formnumber',
        'password'       => 'formpassword',
        'radio'          => 'formradio',
        'range'          => 'formrange',
        'reset'          => 'formreset',
        'search'         => 'formsearch',
        'select'         => 'formselect',
        'submit'         => 'formsubmit',
        'tel'            => 'formtel',
        'text'           => 'formtext',
        'textarea'       => 'formtextarea',
        'time'           => 'formtime',
        'url'            => 'formurl',
        'week'           => 'formweek',
    ];

    /**
     * Default helper name
     *
     * @var string
     */
    protected $defaultHelper = self::DEFAULT_HELPER;

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|self
     */
    public function __invoke(?ElementInterface $element = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     */
    public function render(ElementInterface $element): string
    {
        $renderer = $this->getView();
        if ($renderer === null || ! method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $renderedInstance = $this->renderInstance($element);

        if ($renderedInstance !== null) {
            return $renderedInstance;
        }

        $renderedType = $this->renderType($element);

        if ($renderedType !== null) {
            return $renderedType;
        }

        return $this->renderHelper($this->defaultHelper, $element);
    }

    /**
     * Set default helper name
     *
     * @return $this
     */
    public function setDefaultHelper(string $name)
    {
        $this->defaultHelper = $name;

        return $this;
    }

    /**
     * Add form element type to plugin map
     *
     * @return $this
     */
    public function addType(string $type, string $plugin)
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance class to plugin map
     *
     * @return $this
     */
    public function addClass(string $class, string $plugin)
    {
        $this->classMap[$class] = $plugin;

        return $this;
    }

    /**
     * Render element by helper name
     */
    protected function renderHelper(string $name, ElementInterface $element): string
    {
        $renderer = $this->getView();
        assert($renderer instanceof PhpRenderer);
        $helper = $renderer->plugin($name);
        assert(is_callable($helper));
        return $helper($element);
    }

    /**
     * Render element by instance map
     */
    protected function renderInstance(ElementInterface $element): ?string
    {
        foreach ($this->classMap as $class => $pluginName) {
            if ($element instanceof $class) {
                return $this->renderHelper($pluginName, $element);
            }
        }

        return null;
    }

    /**
     * Render element by type map
     */
    protected function renderType(ElementInterface $element): ?string
    {
        $type = $element->getAttribute('type');

        if (isset($this->typeMap[$type])) {
            return $this->renderHelper($this->typeMap[$type], $element);
        }

        return null;
    }
}
