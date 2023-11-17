<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Escaper\Exception\RuntimeException as EscaperException;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\I18n\View\Helper\AbstractTranslatorHelper as BaseAbstractHelper;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;

use function implode;
use function in_array;
use function is_bool;
use function is_string;
use function mb_strpos;
use function method_exists;
use function preg_match;
use function sprintf;
use function str_starts_with;
use function strtolower;

/**
 * Base functionality for all form view helpers
 */
abstract class AbstractHelper extends BaseAbstractHelper
{
    /**
     * The default translatable HTML attributes
     *
     * @var array
     */
    protected static $defaultTranslatableHtmlAttributes = [
        'title' => true,
    ];

    /**
     * The default translatable HTML attribute prefixes
     *
     * @var array
     */
    protected static $defaultTranslatableHtmlAttributePrefixes = [];

    /**
     * Standard boolean attributes, with expected values for enabling/disabling
     *
     * @var array
     */
    protected $booleanAttributes = [
        'autofocus' => ['on' => 'autofocus', 'off' => ''], // https://html.spec.whatwg.org/#attr-fe-autofocus
        'checked'   => ['on' => 'checked',   'off' => ''], // https://html.spec.whatwg.org/#attr-input-checked
        'disabled'  => ['on' => 'disabled',  'off' => ''], // https://html.spec.whatwg.org/#attr-fe-disabled
        'itemscope' => ['on' => 'itemscope', 'off' => ''], // https://html.spec.whatwg.org/#attr-itemscope
        'multiple'  => ['on' => 'multiple',  'off' => ''], // https://html.spec.whatwg.org/#attr-input-multiple
        'readonly'  => ['on' => 'readonly',  'off' => ''], // https://html.spec.whatwg.org/#attr-input-readonly
        'required'  => ['on' => 'required',  'off' => ''], // https://html.spec.whatwg.org/#attr-input-required
        'selected'  => ['on' => 'selected',  'off' => ''], // https://html.spec.whatwg.org/#attr-option-selected
    ];

    /**
     * Translatable attributes
     *
     * @var array<string, bool>
     */
    protected $translatableAttributes = [
        'placeholder' => true,
    ];

    /**
     * Prefixes of translatable HTML attributes
     *
     * @var array
     */
    protected $translatableAttributePrefixes = [];

    /** @var null|Doctype */
    protected $doctypeHelper;

    /** @var null|EscapeHtml */
    protected $escapeHtmlHelper;

    /** @var null|EscapeHtmlAttr */
    protected $escapeHtmlAttrHelper;

    /**
     * Attributes globally valid for all tags
     *
     * @var array
     */
    protected $validGlobalAttributes = [
        'accesskey'       => true, // https://html.spec.whatwg.org/#the-accesskey-attribute
        'autocapitalize'  => true, // https://html.spec.whatwg.org/#attr-autocapitalize
        'class'           => true, // https://html.spec.whatwg.org/#classes
        'contenteditable' => true, // https://html.spec.whatwg.org/#contenteditable
        'contextmenu'     => true, // Obsolete: https://html.spec.whatwg.org/#attr-contextmenu
        'dir'             => true, // https://html.spec.whatwg.org/#the-dir-attribute
        'draggable'       => true, // https://html.spec.whatwg.org/#the-draggable-attribute
        'dropzone'        => true, // Obsolete: https://html.spec.whatwg.org/#attr-dropzone
        'enterkeyhint'    => true, // https://html.spec.whatwg.org/#input-modalities:-the-enterkeyhint-attribute
        'hidden'          => true, // https://html.spec.whatwg.org/#the-hidden-attribute
        'id'              => true, // https://html.spec.whatwg.org/#the-id-attribute
        'inputmode'       => true, // https://html.spec.whatwg.org/#attr-inputmode
        'is'              => true, // https://html.spec.whatwg.org/#attr-is
        //'itemid'             => true, // https://html.spec.whatwg.org/#attr-itemid
        'itemprop'  => true, // https://html.spec.whatwg.org/#names:-the-itemprop-attribute
        'itemref'   => true, // https://html.spec.whatwg.org/#attr-itemref
        'itemscope' => true, // https://html.spec.whatwg.org/#attr-itemscope
        'itemtype'  => true, // https://html.spec.whatwg.org/#attr-itemtype
        'lang'      => true, // https://html.spec.whatwg.org/#the-lang-and-xml:lang-attributes
        //'nonce'              => true, // https://html.spec.whatwg.org/#attr-nonce
        'onabort'            => true, // https://html.spec.whatwg.org/#handler-onabort
        'onblur'             => true, // https://html.spec.whatwg.org/#handler-onblur
        'oncanplay'          => true, // https://html.spec.whatwg.org/#handler-oncanplay
        'oncanplaythrough'   => true, // https://html.spec.whatwg.org/#handler-oncanplaythrough
        'onchange'           => true, // https://html.spec.whatwg.org/#handler-onchange
        'onclick'            => true, // https://html.spec.whatwg.org/#handler-onclick
        'oncontextmenu'      => true, // https://html.spec.whatwg.org/#handler-oncontextmenu
        'ondblclick'         => true, // https://html.spec.whatwg.org/#handler-ondblclick
        'ondrag'             => true, // https://html.spec.whatwg.org/#handler-ondrag
        'ondragend'          => true, // https://html.spec.whatwg.org/#handler-ondragend
        'ondragenter'        => true, // https://html.spec.whatwg.org/#handler-ondragenter
        'ondragleave'        => true, // https://html.spec.whatwg.org/#handler-ondragleave
        'ondragover'         => true, // https://html.spec.whatwg.org/#handler-ondragover
        'ondragstart'        => true, // https://html.spec.whatwg.org/#handler-ondragstart
        'ondrop'             => true, // https://html.spec.whatwg.org/#handler-ondrop
        'ondurationchange'   => true, // https://html.spec.whatwg.org/#handler-ondurationchange
        'onemptied'          => true, // https://html.spec.whatwg.org/#handler-onemptied
        'onended'            => true, // https://html.spec.whatwg.org/#handler-onended
        'onerror'            => true, // https://html.spec.whatwg.org/#handler-onerror
        'onfocus'            => true, // https://html.spec.whatwg.org/#handler-onfocus
        'onfocusin'          => true, // https://developer.mozilla.org/fr/docs/Web/API/Element/focusin_event
        'onfocusout'         => true, // https://developer.mozilla.org/fr/docs/Web/API/Element/focusout_event
        'oninput'            => true, // https://html.spec.whatwg.org/#handler-oninput
        'oninvalid'          => true, // https://html.spec.whatwg.org/#handler-oninvalid
        'onkeydown'          => true, // https://html.spec.whatwg.org/#handler-onkeydown
        'onkeypress'         => true, // https://html.spec.whatwg.org/#handler-onkeypress
        'onkeyup'            => true, // https://html.spec.whatwg.org/#handler-onkeyup
        'onload'             => true, // https://html.spec.whatwg.org/#handler-onload
        'onloadeddata'       => true, // https://html.spec.whatwg.org/#handler-onloadeddata
        'onloadedmetadata'   => true, // https://html.spec.whatwg.org/#handler-onloadedmetadata
        'onloadstart'        => true, // https://html.spec.whatwg.org/#handler-onloadstart
        'onmousedown'        => true, // https://html.spec.whatwg.org/#handler-onmousedown
        'onmousemove'        => true, // https://html.spec.whatwg.org/#handler-onmousemove
        'onmouseout'         => true, // https://html.spec.whatwg.org/#handler-onmouseout
        'onmouseover'        => true, // https://html.spec.whatwg.org/#handler-onmouseover
        'onmouseup'          => true, // https://html.spec.whatwg.org/#handler-onmouseup
        'onmousewheel'       => true, // https://html.spec.whatwg.org/#handler-onmousewheel
        'onpause'            => true, // https://html.spec.whatwg.org/#handler-onpause
        'onplay'             => true, // https://html.spec.whatwg.org/#handler-onplay
        'onplaying'          => true, // https://html.spec.whatwg.org/#handler-onplaying
        'onprogress'         => true, // https://html.spec.whatwg.org/#handler-onprogress
        'onratechange'       => true, // https://html.spec.whatwg.org/#handler-onratechange
        'onreadystatechange' => true, // https://html.spec.whatwg.org/#handler-onreadystatechange
        'onreset'            => true, // https://html.spec.whatwg.org/#handler-onreset
        'onscroll'           => true, // https://html.spec.whatwg.org/#handler-onscroll
        'onseeked'           => true, // https://html.spec.whatwg.org/#handler-onseeked
        'onseeking'          => true, // https://html.spec.whatwg.org/#handler-onseeking
        'onselect'           => true, // https://html.spec.whatwg.org/#handler-onselect
        'onshow'             => true, // https://html.spec.whatwg.org/#handler-onshow
        'onstalled'          => true, // https://html.spec.whatwg.org/#handler-onstalled
        'onsubmit'           => true, // https://html.spec.whatwg.org/#handler-onsubmit
        'onsuspend'          => true, // https://html.spec.whatwg.org/#handler-onsuspend
        'ontimeupdate'       => true, // https://html.spec.whatwg.org/#handler-ontimeupdate
        'onvolumechange'     => true, // https://html.spec.whatwg.org/#handler-onvolumechange
        'onwaiting'          => true, // https://html.spec.whatwg.org/#handler-onwaiting
        'role'               => true, // https://html.spec.whatwg.org/#attr-aria-role
        'slot'               => true, // https://html.spec.whatwg.org/#attr-slot
        'spellcheck'         => true, // https://html.spec.whatwg.org/#attr-spellcheck
        'style'              => true, // https://html.spec.whatwg.org/#attr-style
        'tabindex'           => true, // https://html.spec.whatwg.org/#attr-tabindex
        'title'              => true, // https://html.spec.whatwg.org/#attr-title
        //'translate'          => true, // https://html.spec.whatwg.org/#attr-translate
        'xml:base'  => true, // https://www.w3.org/TR/xmlbase/#syntax
        'xml:lang'  => true, // https://html.spec.whatwg.org/#the-lang-and-xml:lang-attributes
        'xml:space' => true, // https://www.w3.org/TR/xml/#sec-white-space
    ];

    /**
     * Attribute prefixes valid for all tags
     *
     * @var array
     */
    protected $validTagAttributePrefixes = [
        'data-', // https://html.spec.whatwg.org/#attr-data-*
        'aria-', // https://html.spec.whatwg.org/#attr-aria-*
        'x-',
    ];

    /**
     * Attributes valid for the tag represented by this helper
     *
     * This should be overridden in extending classes
     *
     * @var array
     */
    protected $validTagAttributes = [];

    /**
     * Set value for doctype
     *
     * @return $this
     */
    public function setDoctype(string $doctype)
    {
        $this->getDoctypeHelper()->setDoctype($doctype);
        return $this;
    }

    /**
     * Get value for doctype
     */
    public function getDoctype(): string
    {
        return $this->getDoctypeHelper()->getDoctype();
    }

    /**
     * Set value for character encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding)
    {
        $this->getEscapeHtmlHelper()->setEncoding($encoding);
        $this->getEscapeHtmlAttrHelper()->setEncoding($encoding);
        return $this;
    }

    /**
     * Get character encoding
     */
    public function getEncoding(): string
    {
        return $this->getEscapeHtmlHelper()->getEncoding();
    }

    /**
     * Create a string of all attribute/value pairs
     *
     * Escapes all attribute values
     *
     * @param  array $attributes
     */
    public function createAttributesString(array $attributes): string
    {
        $attributes    = $this->prepareAttributes($attributes);
        $escape        = $this->getEscapeHtmlHelper();
        $escapeAttr    = $this->getEscapeHtmlAttrHelper();
        $doctypeHelper = $this->getDoctypeHelper();
        $strings       = [];

        foreach ($attributes as $key => $value) {
            $key = strtolower($key);

            if (isset($this->booleanAttributes[$key])) {
                if (! $value) {
                    // Skip boolean attributes that expect empty string as false value
                    if ('' === $this->booleanAttributes[$key]['off']) {
                        continue;
                    }
                } elseif ($doctypeHelper->isHtml5() && ! $doctypeHelper->isXhtml()) {
                    $strings[] = $escape($key);
                    continue;
                }
            }

            //check if attribute is translatable and translate it
            $value = $this->translateHtmlAttributeValue($key, $value);

            // @todo Escape event attributes like AbstractHtmlElement view helper does in htmlAttribs ??
            try {
                $escapedAttribute = $escapeAttr($value);
                $strings[]        = sprintf('%s="%s"', $escape($key), $escapedAttribute);
            } catch (EscaperException) {
                // If an escaper exception happens, escape only the key, and use a blank value.
                $strings[] = sprintf('%s=""', $escape($key));
            }
        }

        return implode(' ', $strings);
    }

    /**
     * Get the ID of an element
     *
     * If no ID attribute present, attempts to use the name attribute.
     * If no name attribute is present, either, returns null.
     */
    public function getId(ElementInterface $element): ?string
    {
        $id = $element->getAttribute('id');
        if (is_string($id) && $id !== '') {
            return $id;
        }

        return $element->getName();
    }

    /**
     * Get the closing bracket for an inline tag
     *
     * Closes as either "/>" for XHTML doctypes or ">" otherwise.
     */
    public function getInlineClosingBracket(): string
    {
        $doctypeHelper = $this->getDoctypeHelper();
        if ($doctypeHelper->isXhtml()) {
            return '/>';
        }
        return '>';
    }

    /**
     * Retrieve the doctype helper
     */
    protected function getDoctypeHelper(): Doctype
    {
        if ($this->doctypeHelper) {
            return $this->doctypeHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->doctypeHelper = $this->view->plugin('doctype');
        }

        if (! $this->doctypeHelper instanceof Doctype) {
            $this->doctypeHelper = new Doctype();
        }

        return $this->doctypeHelper;
    }

    /**
     * Retrieve the escapeHtml helper
     */
    protected function getEscapeHtmlHelper(): EscapeHtml
    {
        if ($this->escapeHtmlHelper) {
            return $this->escapeHtmlHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->escapeHtmlHelper = $this->view->plugin('escapehtml');
        }

        if (! $this->escapeHtmlHelper instanceof EscapeHtml) {
            $this->escapeHtmlHelper = new EscapeHtml();
        }

        return $this->escapeHtmlHelper;
    }

    /**
     * Retrieve the escapeHtmlAttr helper
     */
    protected function getEscapeHtmlAttrHelper(): EscapeHtmlAttr
    {
        if ($this->escapeHtmlAttrHelper) {
            return $this->escapeHtmlAttrHelper;
        }

        if ($this->view !== null && method_exists($this->view, 'plugin')) {
            $this->escapeHtmlAttrHelper = $this->view->plugin('escapehtmlattr');
        }

        if (! $this->escapeHtmlAttrHelper instanceof EscapeHtmlAttr) {
            $this->escapeHtmlAttrHelper = new EscapeHtmlAttr();
        }

        return $this->escapeHtmlAttrHelper;
    }

    /**
     * Prepare attributes for rendering
     *
     * Ensures appropriate attributes are present (e.g., if "name" is present,
     * but no "id", sets the latter to the former).
     *
     * Removes any invalid attributes
     *
     * @param  array $attributes
     * @return array
     */
    protected function prepareAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            $attribute = strtolower($key);

            if (
                ! isset($this->validGlobalAttributes[$attribute])
                && ! isset($this->validTagAttributes[$attribute])
                && ! $this->hasAllowedPrefix($attribute)
            ) {
                unset($attributes[$key]);
                continue;
            }

            // Normalize attribute key, if needed
            if ($attribute !== $key) {
                unset($attributes[$key]);
                $attributes[$attribute] = $value;
            }

            // Normalize boolean attribute values
            if (isset($this->booleanAttributes[$attribute])) {
                $attributes[$attribute] = $this->prepareBooleanAttributeValue($attribute, $value);
            } elseif (! is_string($value)) {
                $attributes[$attribute] = (string) $value;
            }
        }

        return $attributes;
    }

    /**
     * Prepare a boolean attribute value
     *
     * Prepares the expected representation for the boolean attribute specified.
     */
    protected function prepareBooleanAttributeValue(string $attribute, mixed $value): string
    {
        if (! is_bool($value) && in_array($value, $this->booleanAttributes[$attribute], true)) {
            return $value;
        }

        $value = (bool) $value;
        return $value
            ? $this->booleanAttributes[$attribute]['on']
            : $this->booleanAttributes[$attribute]['off'];
    }

    /**
     * Translates the value of the HTML attribute if it should be translated and this view helper has a translator
     */
    protected function translateHtmlAttributeValue(string $key, ?string $value): ?string
    {
        if (empty($value) || ($this->getTranslator() === null)) {
            return $value;
        }

        if (isset($this->translatableAttributes[$key]) || isset(self::$defaultTranslatableHtmlAttributes[$key])) {
            return $this->getTranslator()->translate($value, $this->getTranslatorTextDomain());
        } else {
            foreach ($this->translatableAttributePrefixes as $prefix) {
                if (0 === mb_strpos($key, $prefix)) {
                    // prefix matches => return translated $value
                    return $this->getTranslator()->translate($value, $this->getTranslatorTextDomain());
                }
            }
            foreach (self::$defaultTranslatableHtmlAttributePrefixes as $prefix) {
                if (0 === mb_strpos($key, $prefix)) {
                    // default prefix matches => return translated $value
                    return $this->getTranslator()->translate($value, $this->getTranslatorTextDomain());
                }
            }
        }

        return $value;
    }

    /**
     * Adds an HTML attribute to the list of valid attributes
     *
     * @return $this
     * @throws InvalidArgumentException For attribute names that are invalid per the HTML specifications.
     */
    public function addValidAttribute(string $attribute)
    {
        if (! $this->isValidAttributeName($attribute)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid attribute name', $attribute));
        }

        $this->validTagAttributes[$attribute] = true;
        return $this;
    }

    /**
     * Adds a prefix to the list of valid attribute prefixes
     *
     * @return $this
     * @throws InvalidArgumentException For attribute prefixes that are invalid
     *                                  per the HTML specifications for attribute names.
     */
    public function addValidAttributePrefix(string $prefix)
    {
        if (! $this->isValidAttributeName($prefix)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid attribute prefix', $prefix));
        }

        $this->validTagAttributePrefixes[] = $prefix;
        return $this;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes
     *
     * @return $this
     */
    public function addTranslatableAttribute(string $attribute)
    {
        $this->translatableAttributes[$attribute] = true;

        return $this;
    }

    /**
     * Adds an HTML attribute to the list of the default translatable attributes
     */
    public static function addDefaultTranslatableAttribute(string $attribute): void
    {
        self::$defaultTranslatableHtmlAttributes[$attribute] = true;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes
     *
     * @return $this
     */
    public function addTranslatableAttributePrefix(string $prefix)
    {
        $this->translatableAttributePrefixes[] = $prefix;

        return $this;
    }

    /**
     * Adds an HTML attribute to the list of translatable attributes
     */
    public static function addDefaultTranslatableAttributePrefix(string $prefix): void
    {
        self::$defaultTranslatableHtmlAttributePrefixes[] = $prefix;
    }

    /**
     * Whether the passed attribute is valid or not
     *
     * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2
     *     Description of valid attributes
     */
    protected function isValidAttributeName(string $attribute): bool
    {
        return (bool) preg_match('/^[^\t\n\f \/>"\'=]+$/', $attribute);
    }

    /**
     * Whether the passed attribute has a valid prefix or not
     */
    protected function hasAllowedPrefix(string $attribute): bool
    {
        foreach ($this->validTagAttributePrefixes as $prefix) {
            if (str_starts_with($attribute, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
