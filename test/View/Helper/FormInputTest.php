<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormInput as FormInputHelper;

use function sprintf;

class FormInputTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormInputHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesTextInputTagWhenProvidedAnElementWithNoTypeAttribute()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="text"', $markup);
    }

    public function testGeneratesInputTagWithElementsTypeAttribute()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="email"', $markup);
    }

    public function inputTypes()
    {
        return [
            ['text', 'assertStringContainsString'],
            ['button', 'assertStringContainsString'],
            ['checkbox', 'assertStringContainsString'],
            ['file', 'assertStringContainsString'],
            ['hidden', 'assertStringContainsString'],
            ['image', 'assertStringContainsString'],
            ['password', 'assertStringContainsString'],
            ['radio', 'assertStringContainsString'],
            ['reset', 'assertStringContainsString'],
            ['select', 'assertStringContainsString'],
            ['submit', 'assertStringContainsString'],
            ['color', 'assertStringContainsString'],
            ['date', 'assertStringContainsString'],
            ['datetime', 'assertStringContainsString'],
            ['datetime-local', 'assertStringContainsString'],
            ['email', 'assertStringContainsString'],
            ['month', 'assertStringContainsString'],
            ['number', 'assertStringContainsString'],
            ['range', 'assertStringContainsString'],
            ['search', 'assertStringContainsString'],
            ['tel', 'assertStringContainsString'],
            ['time', 'assertStringContainsString'],
            ['url', 'assertStringContainsString'],
            ['week', 'assertStringContainsString'],
            ['lunar', 'assertStringNotContainsString'],
            ['name', 'assertStringNotContainsString'],
            ['username', 'assertStringNotContainsString'],
            ['address', 'assertStringNotContainsString'],
            ['homepage', 'assertStringNotContainsString'],
        ];
    }

    /**
     * @dataProvider inputTypes
     */
    public function testOnlyAllowsValidInputTypes($type, $assertion)
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->render($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    /**
     * @return array
     */
    public function validAttributes()
    {
        return [
            ['accept', 'assertStringContainsString'],
            ['accesskey', 'assertStringContainsString'],
            ['alt', 'assertStringContainsString'],
            ['autocomplete', 'assertStringContainsString'],
            ['autofocus', 'assertStringContainsString'],
            ['checked', 'assertStringContainsString'],
            ['class', 'assertStringContainsString'],
            ['contenteditable', 'assertStringContainsString'],
            ['contextmenu', 'assertStringContainsString'],
            ['dir', 'assertStringContainsString'],
            ['dirname', 'assertStringContainsString'],
            ['disabled', 'assertStringContainsString'],
            ['draggable', 'assertStringContainsString'],
            ['dropzone', 'assertStringContainsString'],
            ['form', 'assertStringContainsString'],
            ['formaction', 'assertStringContainsString'],
            ['formenctype', 'assertStringContainsString'],
            ['formmethod', 'assertStringContainsString'],
            ['formnovalidate', 'assertStringContainsString'],
            ['formtarget', 'assertStringContainsString'],
            ['height', 'assertStringContainsString'],
            ['hidden', 'assertStringContainsString'],
            ['id', 'assertStringContainsString'],
            ['lang', 'assertStringContainsString'],
            ['list', 'assertStringContainsString'],
            ['max', 'assertStringContainsString'],
            ['maxlength', 'assertStringContainsString'],
            ['min', 'assertStringContainsString'],
            ['multiple', 'assertStringContainsString'],
            ['name', 'assertStringContainsString'],
            ['onabort', 'assertStringContainsString'],
            ['onblur', 'assertStringContainsString'],
            ['oncanplay', 'assertStringContainsString'],
            ['oncanplaythrough', 'assertStringContainsString'],
            ['onchange', 'assertStringContainsString'],
            ['onclick', 'assertStringContainsString'],
            ['oncontextmenu', 'assertStringContainsString'],
            ['ondblclick', 'assertStringContainsString'],
            ['ondrag', 'assertStringContainsString'],
            ['ondragend', 'assertStringContainsString'],
            ['ondragenter', 'assertStringContainsString'],
            ['ondragleave', 'assertStringContainsString'],
            ['ondragover', 'assertStringContainsString'],
            ['ondragstart', 'assertStringContainsString'],
            ['ondrop', 'assertStringContainsString'],
            ['ondurationchange', 'assertStringContainsString'],
            ['onemptied', 'assertStringContainsString'],
            ['onended', 'assertStringContainsString'],
            ['onerror', 'assertStringContainsString'],
            ['onfocus', 'assertStringContainsString'],
            ['oninput', 'assertStringContainsString'],
            ['oninvalid', 'assertStringContainsString'],
            ['onkeydown', 'assertStringContainsString'],
            ['onkeypress', 'assertStringContainsString'],
            ['onkeyup', 'assertStringContainsString'],
            ['onload', 'assertStringContainsString'],
            ['onloadeddata', 'assertStringContainsString'],
            ['onloadedmetadata', 'assertStringContainsString'],
            ['onloadstart', 'assertStringContainsString'],
            ['onmousedown', 'assertStringContainsString'],
            ['onmousemove', 'assertStringContainsString'],
            ['onmouseout', 'assertStringContainsString'],
            ['onmouseover', 'assertStringContainsString'],
            ['onmouseup', 'assertStringContainsString'],
            ['onmousewheel', 'assertStringContainsString'],
            ['onpause', 'assertStringContainsString'],
            ['onplay', 'assertStringContainsString'],
            ['onplaying', 'assertStringContainsString'],
            ['onprogress', 'assertStringContainsString'],
            ['onratechange', 'assertStringContainsString'],
            ['onreadystatechange', 'assertStringContainsString'],
            ['onreset', 'assertStringContainsString'],
            ['onscroll', 'assertStringContainsString'],
            ['onseeked', 'assertStringContainsString'],
            ['onseeking', 'assertStringContainsString'],
            ['onselect', 'assertStringContainsString'],
            ['onshow', 'assertStringContainsString'],
            ['onstalled', 'assertStringContainsString'],
            ['onsubmit', 'assertStringContainsString'],
            ['onsuspend', 'assertStringContainsString'],
            ['ontimeupdate', 'assertStringContainsString'],
            ['onvolumechange', 'assertStringContainsString'],
            ['onwaiting', 'assertStringContainsString'],
            ['role', 'assertStringContainsString'],
            ['itemprop', 'assertStringContainsString'],
            ['itemscope', 'assertStringContainsString'],
            ['itemtype', 'assertStringContainsString'],
        ];
    }

    public function validAttributes2()
    {
        return [
            ['pattern', 'assertStringContainsString'],
            ['placeholder', 'assertStringContainsString'],
            ['readonly', 'assertStringContainsString'],
            ['required', 'assertStringContainsString'],
            ['size', 'assertStringContainsString'],
            ['spellcheck', 'assertStringContainsString'],
            ['src', 'assertStringContainsString'],
            ['step', 'assertStringContainsString'],
            ['style', 'assertStringContainsString'],
            ['tabindex', 'assertStringContainsString'],
            ['title', 'assertStringContainsString'],
            ['value', 'assertStringContainsString'],
            ['width', 'assertStringContainsString'],
            ['xml:base', 'assertStringContainsString'],
            ['xml:lang', 'assertStringContainsString'],
            ['xml:space', 'assertStringContainsString'],
            ['data-some-key', 'assertStringContainsString'],
            ['option', 'assertStringNotContainsString'],
            ['optgroup', 'assertStringNotContainsString'],
            ['arbitrary', 'assertStringNotContainsString'],
            ['meta', 'assertStringNotContainsString'],
            ['role', 'assertStringContainsString'],
        ];
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accept'             => 'value',
            'accesskey'          => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'postal-code',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
            'onabort'            => 'value',
            'onblur'             => 'value',
            'oncanplay'          => 'value',
            'oncanplaythrough'   => 'value',
            'onchange'           => 'value',
            'onclick'            => 'value',
            'oncontextmenu'      => 'value',
            'ondblclick'         => 'value',
            'ondrag'             => 'value',
            'ondragend'          => 'value',
            'ondragenter'        => 'value',
            'ondragleave'        => 'value',
            'ondragover'         => 'value',
            'ondragstart'        => 'value',
            'ondrop'             => 'value',
            'ondurationchange'   => 'value',
            'onemptied'          => 'value',
            'onended'            => 'value',
            'onerror'            => 'value',
            'onfocus'            => 'value',
            'oninput'            => 'value',
            'oninvalid'          => 'value',
            'onkeydown'          => 'value',
            'onkeypress'         => 'value',
            'onkeyup'            => 'value',
            'onload'             => 'value',
            'onloadeddata'       => 'value',
            'onloadedmetadata'   => 'value',
            'onloadstart'        => 'value',
            'onmousedown'        => 'value',
            'onmousemove'        => 'value',
            'onmouseout'         => 'value',
            'onmouseover'        => 'value',
            'onmouseup'          => 'value',
            'onmousewheel'       => 'value',
            'onpause'            => 'value',
            'onplay'             => 'value',
            'onplaying'          => 'value',
            'onprogress'         => 'value',
            'onratechange'       => 'value',
            'onreadystatechange' => 'value',
            'onreset'            => 'value',
            'onscroll'           => 'value',
            'onseeked'           => 'value',
            'onseeking'          => 'value',
            'onselect'           => 'value',
            'onshow'             => 'value',
            'onstalled'          => 'value',
            'onsubmit'           => 'value',
            'onsuspend'          => 'value',
            'ontimeupdate'       => 'value',
            'onvolumechange'     => 'value',
            'onwaiting'          => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'spellcheck'         => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'width'              => 'value',
            'wrap'               => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
            'option'             => 'value',
            'optgroup'           => 'value',
            'arbitrary'          => 'value',
            'meta'               => 'value',
            'role'               => 'value',
            'itemprop'           => 'value',
            'itemscope'          => 'itemscope',
            'itemtype'           => 'value',
        ]);
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     * @return       void
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            case 'value':
                $expect  = sprintf(' %s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect  = sprintf(' %s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    public function nonXhtmlDoctypes()
    {
        return [
            ['HTML4_STRICT'],
            ['HTML4_LOOSE'],
            ['HTML4_FRAMESET'],
            ['HTML5'],
        ];
    }

    /**
     * @dataProvider nonXhtmlDoctypes
     */
    public function testRenderingOmitsClosingSlashWhenDoctypeIsNotXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertStringNotContainsString('/>', $markup);
    }

    public function xhtmlDoctypes()
    {
        return [
            ['XHTML11'],
            ['XHTML1_STRICT'],
            ['XHTML1_TRANSITIONAL'],
            ['XHTML1_FRAMESET'],
            ['XHTML1_RDFA'],
            ['XHTML_BASIC1'],
            ['XHTML5'],
        ];
    }

    /**
     * @dataProvider xhtmlDoctypes
     */
    public function testRenderingIncludesClosingSlashWhenDoctypeIsXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('/>', $markup);
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function booleanAttributeTypes()
    {
        return [
            ['autofocus', 'autofocus', ''],
            ['disabled', 'disabled', ''],
            ['multiple', 'multiple', ''],
            ['readonly', 'readonly', ''],
            ['required', 'required', ''],
            ['checked', 'checked', ''],
            ['itemscope', 'itemscope', ''],
        ];
    }

    /**
     * @group Laminas-391
     * @dataProvider booleanAttributeTypes
     */
    public function testBooleanAttributeTypesAreRenderedCorrectly($attribute, $on, $off)
    {
        $element = new Element('foo');
        $element->setAttribute($attribute, true);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertStringContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup)
        );

        $element->setAttribute($attribute, false);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $off);

        if ($off !== '') {
            $this->assertStringContainsString(
                $expect,
                $markup,
                sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup)
            );
        } else {
            $this->assertStringNotContainsString(
                $expect,
                $markup,
                sprintf('Disabled value for %s should not be rendered; received %s', $attribute, $markup)
            );
        }

        // Laminas-391 : Ability to use non-boolean values that match expected end-value
        $element->setAttribute($attribute, $on);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertStringContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup)
        );

        $element->setAttribute($attribute, $off);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $off);

        if ($off !== '') {
            $this->assertStringContainsString(
                $expect,
                $markup,
                sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup)
            );
        } else {
            $this->assertStringNotContainsString(
                $expect,
                $markup,
                sprintf('Disabled value for %s should not be rendered; received %s', $attribute, $markup)
            );
        }
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<input', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    /**
     * @group Laminas-489
     */
    public function testCanTranslatePlaceholder()
    {
        $element = new Element('test');
        $element->setAttribute('placeholder', 'test');

        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');

        $mockTranslator->expects($this->once())
                ->method('translate')
                ->willReturn('translated string');

        $this->helper->setTranslator($mockTranslator);

        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);

        $this->assertStringContainsString('placeholder="translated&#x20;string"', $markup);
    }

    public function testCanTranslateTitle()
    {
        $element = new Element('test');
        $element->setAttribute('title', 'test');

        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');

        $mockTranslator->expects($this->once())
                ->method('translate')
                ->with($this->equalTo('test'))
                ->willReturn('translated string');

        $this->helper->setTranslator($mockTranslator);

        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);

        $this->assertStringContainsString('title="translated&#x20;string"', $markup);
    }

    /**
     * @group issue-7166
     */
    public function testPasswordValueShouldNotBeRendered()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'password');

        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('value=""', $markup);
    }
}
