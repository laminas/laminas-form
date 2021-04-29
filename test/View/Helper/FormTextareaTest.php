<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormTextarea as FormTextareaHelper;

use function sprintf;

class FormTextareaTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormTextareaHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesEmptyTextareaWhenNoValueAttributePresent()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#<textarea.*?></textarea>#', $markup);
    }

    public function validAttributes()
    {
        return [
            ['accesskey', 'assertStringContainsString'],
            ['class', 'assertStringContainsString'],
            ['contenteditable', 'assertStringContainsString'],
            ['contextmenu', 'assertStringContainsString'],
            ['dir', 'assertStringContainsString'],
            ['draggable', 'assertStringContainsString'],
            ['dropzone', 'assertStringContainsString'],
            ['hidden', 'assertStringContainsString'],
            ['id', 'assertStringContainsString'],
            ['lang', 'assertStringContainsString'],
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
            ['spellcheck', 'assertStringContainsString'],
            ['style', 'assertStringContainsString'],
            ['tabindex', 'assertStringContainsString'],
            ['title', 'assertStringContainsString'],
            ['xml:base', 'assertStringContainsString'],
            ['xml:lang', 'assertStringContainsString'],
            ['xml:space', 'assertStringContainsString'],
            ['data-some-key', 'assertStringContainsString'],
            ['autofocus', 'assertStringContainsString'],
            ['cols', 'assertStringContainsString'],
            ['dirname', 'assertStringContainsString'],
            ['disabled', 'assertStringContainsString'],
            ['form', 'assertStringContainsString'],
            ['maxlength', 'assertStringContainsString'],
            ['minlength', 'assertStringContainsString'],
            ['name', 'assertStringContainsString'],
            ['placeholder', 'assertStringContainsString'],
            ['readonly', 'assertStringContainsString'],
            ['required', 'assertStringContainsString'],
            ['rows', 'assertStringContainsString'],
            ['wrap', 'assertStringContainsString'],
            ['content', 'assertStringNotContainsString'],
            ['option', 'assertStringNotContainsString'],
            ['optgroup', 'assertStringNotContainsString'],
            ['arbitrary', 'assertStringNotContainsString'],
            ['meta', 'assertStringNotContainsString'],
            ['role', 'assertStringContainsString'],
            ['inputmode', 'assertStringContainsString'],
        ];
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accesskey'          => 'value',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
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
            'spellcheck'         => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
            'autofocus'          => 'autofocus',
            'cols'               => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'maxlength'          => 'value',
            'minlength'          => 'value',
            'name'               => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'rows'               => 'value',
            'wrap'               => 'value',
            'content'            => 'value',
            'option'             => 'value',
            'optgroup'           => 'value',
            'arbitrary'          => 'value',
            'meta'               => 'value',
            'role'               => 'value',
            'inputmode'          => 'value',
        ]);
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
        $this->$assertion($expect, $markup);
    }

    public function booleanAttributeTypes()
    {
        return [
            ['autofocus', 'autofocus', ''],
            ['disabled', 'disabled', ''],
            ['readonly', 'readonly', ''],
            ['required', 'required', ''],
        ];
    }

    /**
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
    }

    public function testRendersValueAttributeAsTextareaContent()
    {
        $element = new Element('foo');
        $element->setAttribute('value', 'Initial content');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('>Initial content</textarea>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<textarea', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
