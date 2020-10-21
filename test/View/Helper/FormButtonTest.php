<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormButton as FormButtonHelper;

use function sprintf;

class FormButtonTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormButtonHelper();
        parent::setUp();
    }

    public function testCanEmitStartTagOnly()
    {
        $markup = $this->helper->openTag();
        $this->assertEquals('<button>', $markup);
    }

    public function testPassingArrayToOpenTagRendersAttributes()
    {
        $attributes = [
            'name'  => 'my-button',
            'class' => 'email-button',
            'type'  => 'button',
        ];
        $markup = $this->helper->openTag($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertStringContainsString(sprintf('%s="%s"', $key, $value), $markup);
        }
    }

    public function testCanEmitCloseTagOnly()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testPassingElementToOpenTagWillUseNameAttribute()
    {
        $element = new Element('foo');
        $markup = $this->helper->openTag($element);
        $this->assertStringContainsString('name="foo"', $markup);
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElementWhenPassedToOpenTag()
    {
        $element = new Element();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->openTag($element);
    }

    public function testOpenTagWithWrongElementRaisesException()
    {
        $element = new ArrayObject();
        $this->expectException('Laminas\Form\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('ArrayObject');
        $this->helper->openTag($element);
    }

    public function testGeneratesSubmitTypeWhenProvidedAnElementWithNoTypeAttribute()
    {
        $element = new Element('foo');
        $markup  = $this->helper->openTag($element);
        $this->assertStringContainsString('<button ', $markup);
        $this->assertStringContainsString('type="submit"', $markup);
    }

    public function testGeneratesButtonTagWithElementsTypeAttribute()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'button');
        $markup  = $this->helper->openTag($element);
        $this->assertStringContainsString('<button ', $markup);
        $this->assertStringContainsString('type="button"', $markup);
    }

    public function inputTypes()
    {
        return [
            ['submit', 'assertStringContainsString'],
            ['button', 'assertStringContainsString'],
            ['reset', 'assertStringContainsString'],
            ['lunar', 'assertStringNotContainsString'],
            ['name', 'assertStringNotContainsString'],
            ['username', 'assertStringNotContainsString'],
            ['text', 'assertStringNotContainsString'],
            ['checkbox', 'assertStringNotContainsString'],
        ];
    }

    /**
     * @dataProvider inputTypes
     */
    public function testOpenTagOnlyAllowsValidButtonTypes($type, $assertion)
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->openTag($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    public function validAttributes()
    {
        return [
            ['name', 'assertStringContainsString'],
            ['accept', 'assertStringNotContainsString'],
            ['alt', 'assertStringNotContainsString'],
            ['autocomplete', 'assertStringNotContainsString'],
            ['autofocus', 'assertStringContainsString'],
            ['checked', 'assertStringNotContainsString'],
            ['dirname', 'assertStringNotContainsString'],
            ['disabled', 'assertStringContainsString'],
            ['form', 'assertStringContainsString'],
            ['formaction', 'assertStringContainsString'],
            ['formenctype', 'assertStringContainsString'],
            ['formmethod', 'assertStringContainsString'],
            ['formnovalidate', 'assertStringContainsString'],
            ['formtarget', 'assertStringContainsString'],
            ['height', 'assertStringNotContainsString'],
            ['list', 'assertStringNotContainsString'],
            ['max', 'assertStringNotContainsString'],
            ['maxlength', 'assertStringNotContainsString'],
            ['min', 'assertStringNotContainsString'],
            ['multiple', 'assertStringNotContainsString'],
            ['pattern', 'assertStringNotContainsString'],
            ['placeholder', 'assertStringNotContainsString'],
            ['readonly', 'assertStringNotContainsString'],
            ['required', 'assertStringNotContainsString'],
            ['size', 'assertStringNotContainsString'],
            ['src', 'assertStringNotContainsString'],
            ['step', 'assertStringNotContainsString'],
            ['value', 'assertStringContainsString'],
            ['width', 'assertStringNotContainsString'],
        ];
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accept'             => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'on',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'id'                 => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'width'              => 'value',
        ]);
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $element->setLabel('{button_content}');
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            case 'value':
                $expect  = sprintf('%s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    public function testRaisesExceptionWhenLabelAttributeIsNotPresentInElement()
    {
        $element = new Element('foo');
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('label');
        $markup = $this->helper->render($element);
    }

    public function testPassingElementToRenderGeneratesButtonMarkup()
    {
        $element = new Element('foo');
        $element->setLabel('{button_content}');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('>{button_content}<', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('</button>', $markup);
    }

    public function testPassingElementAndContentToRenderUsesContent()
    {
        $element = new Element('foo');
        $markup = $this->helper->render($element, '{button_content}');
        $this->assertStringContainsString('>{button_content}<', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('</button>', $markup);
    }

    public function testCallingFromViewHelperCanHandleOpenTagAndCloseTag()
    {
        $helper = $this->helper;
        $markup = $helper()->openTag();
        $this->assertEquals('<button>', $markup);
        $markup = $helper()->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element, '{button_content}');
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('>{button_content}<', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testDoesNotThrowExceptionIfNameIsZero()
    {
        $element = new Element(0);
        $markup = $this->helper->__invoke($element, '{button_content}');
        $this->assertStringContainsString('name="0"', $markup);
    }

    public function testCanTranslateContent()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');

        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');
        $mockTranslator->expects($this->once())
            ->method('translate')
            ->willReturn('translated content');

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertStringContainsString('>translated content<', $markup);
    }

    public function testCanTranslateButtonContentParameter()
    {
        $element = new Element('foo');

        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');
        $mockTranslator->expects($this->once())
            ->method('translate')
            ->willReturn('translated content');

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element, 'translate me');
        $this->assertStringContainsString('>translated content<', $markup);
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->createMock('Laminas\I18n\Translator\Translator');
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }

    public function testLabelIsEscapedByDefault()
    {
        $element = new Element('foo');
        $element->setLabel('<strong>Click me</strong>');
        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression('#<button([^>]*)>&lt;strong&gt;Click me&lt;/strong&gt;<\/button>#', $markup);
    }

    public function testCanDisableLabelHtmlEscape()
    {
        $element = new Element('foo');
        $element->setLabel('<strong>Click me</strong>');
        $element->setLabelOptions(['disable_html_escape' => true]);
        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression('#<button([^>]*)><strong>Click me</strong></button>#', $markup);
    }
}
