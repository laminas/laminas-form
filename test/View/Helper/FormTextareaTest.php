<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormTextarea as FormTextareaHelper;

use function sprintf;

/**
 * @property FormTextareaHelper $helper
 */
class FormTextareaTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormTextareaHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new Element();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesEmptyTextareaWhenNoValueAttributePresent(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#<textarea.*?></textarea>#', $markup);
    }

    public function validAttributes(): array
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
            ['itemprop', 'assertStringContainsString'],
            ['itemscope', 'assertStringContainsString'],
            ['itemtype', 'assertStringContainsString'],
            ['inputmode', 'assertStringContainsString'],
        ];
    }

    public function getCompleteElement(): Element
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
            'itemprop'           => 'value',
            'itemscope'          => 'itemscope',
            'itemtype'           => 'value',
            'inputmode'          => 'value',
        ]);
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered(
        string $attribute,
        string $assertion
    ): void {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
        $this->$assertion($expect, $markup);
    }

    public function booleanAttributeTypes(): array
    {
        return [
            ['autofocus', 'autofocus', ''],
            ['disabled', 'disabled', ''],
            ['readonly', 'readonly', ''],
            ['required', 'required', ''],
            ['itemscope', 'itemscope', ''],
        ];
    }

    /**
     * @dataProvider booleanAttributeTypes
     */
    public function testBooleanAttributeTypesAreRenderedCorrectly(string $attribute, string $on, string $off): void
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

    /**
     * @dataProvider booleanAttributeTypes
     */
    public function testBooleanAttributeTypesAreRenderedCorrectlyWithoutValueForHtml5(
        string $attribute,
        string $on,
        string $off
    ): void {
        $element = new Element('foo');
        $this->renderer->doctype('HTML5');
        $element->setAttribute($attribute, true);
        $markup = $this->helper->render($element);
        $expect = $attribute;

        $this->assertStringContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup)
        );

        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertStringNotContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should not be '%s'; received %s", $attribute, $on, $markup)
        );

        $element->setAttribute($attribute, false);
        $markup = $this->helper->render($element);

        if ($off !== '') {
            $expect = sprintf('%s="%s"', $attribute, $off);

            $this->assertStringContainsString(
                $expect,
                $markup,
                sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup)
            );
        } else {
            $expect = $attribute;

            $this->assertStringNotContainsString(
                $expect,
                $markup,
                sprintf('Disabled value for %s should not be rendered; received %s', $attribute, $markup)
            );
        }

        // Laminas-391 : Ability to use non-boolean values that match expected end-value
        $element->setAttribute($attribute, $on);
        $markup = $this->helper->render($element);
        $expect = $attribute;

        $this->assertStringContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup)
        );

        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertStringNotContainsString(
            $expect,
            $markup,
            sprintf("Enabled value for %s should not be '%s'; received %s", $attribute, $on, $markup)
        );

        $element->setAttribute($attribute, $off);
        $markup = $this->helper->render($element);

        if ($off !== '') {
            $expect = sprintf('%s="%s"', $attribute, $off);

            $this->assertStringContainsString(
                $expect,
                $markup,
                sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup)
            );
        } else {
            $expect = $attribute;

            $this->assertStringNotContainsString(
                $expect,
                $markup,
                sprintf('Disabled value for %s should not be rendered; received %s', $attribute, $markup)
            );
        }
    }

    public function testRendersValueAttributeAsTextareaContent(): void
    {
        $element = new Element('foo');
        $element->setAttribute('value', 'Initial content');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('>Initial content</textarea>', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<textarea', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
