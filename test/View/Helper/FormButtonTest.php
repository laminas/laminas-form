<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormButton as FormButtonHelper;
use Laminas\I18n\Translator\Translator;

use function sprintf;

/**
 * @property FormButtonHelper $helper
 */
final class FormButtonTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormButtonHelper();
        parent::setUp();
    }

    public function testCanEmitStartTagOnly(): void
    {
        $markup = $this->helper->openTag();
        $this->assertEquals('<button>', $markup);
    }

    public function testPassingArrayToOpenTagRendersAttributes(): void
    {
        $attributes = [
            'name'  => 'my-button',
            'class' => 'email-button',
            'type'  => 'button',
        ];
        $markup     = $this->helper->openTag($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertStringContainsString(sprintf('%s="%s"', $key, $value), $markup);
        }
    }

    public function testCanEmitCloseTagOnly(): void
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testPassingElementToOpenTagWillUseNameAttribute(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->openTag($element);
        $this->assertStringContainsString('name="foo"', $markup);
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElementWhenPassedToOpenTag(): void
    {
        $element = new Element();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->openTag($element);
    }

    public function testOpenTagWithWrongElementRaisesException(): void
    {
        $element = new ArrayObject();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(ArrayObject::class);
        $this->helper->openTag($element);
    }

    public function testGeneratesSubmitTypeWhenProvidedAnElementWithNoTypeAttribute(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->openTag($element);
        $this->assertStringContainsString('<button ', $markup);
        $this->assertStringContainsString('type="submit"', $markup);
    }

    public function testGeneratesButtonTagWithElementsTypeAttribute(): void
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'button');
        $markup = $this->helper->openTag($element);
        $this->assertStringContainsString('<button ', $markup);
        $this->assertStringContainsString('type="button"', $markup);
    }

    public function inputTypes(): array
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
    public function testOpenTagOnlyAllowsValidButtonTypes(string $type, string $assertion): void
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->openTag($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    public function validAttributes(): array
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

    public function getCompleteElement(): Element
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accept'         => 'value',
            'alt'            => 'value',
            'autocomplete'   => 'on',
            'autofocus'      => 'autofocus',
            'checked'        => 'checked',
            'dirname'        => 'value',
            'disabled'       => 'disabled',
            'form'           => 'value',
            'formaction'     => 'value',
            'formenctype'    => 'value',
            'formmethod'     => 'value',
            'formnovalidate' => 'value',
            'formtarget'     => 'value',
            'height'         => 'value',
            'id'             => 'value',
            'list'           => 'value',
            'max'            => 'value',
            'maxlength'      => 'value',
            'min'            => 'value',
            'multiple'       => 'multiple',
            'name'           => 'value',
            'pattern'        => 'value',
            'placeholder'    => 'value',
            'readonly'       => 'readonly',
            'required'       => 'required',
            'size'           => 'value',
            'src'            => 'value',
            'step'           => 'value',
            'width'          => 'value',
        ]);
        $element->setValue('value');
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
        $element->setLabel('{button_content}');
        $markup = $this->helper->render($element);
        $expect = match ($attribute) {
            'value' => sprintf('%s="%s"', $attribute, $element->getValue()),
            default => sprintf('%s="%s"', $attribute, $element->getAttribute($attribute)),
        };
        $this->$assertion($expect, $markup);
    }

    public function testRaisesExceptionWhenLabelAttributeIsNotPresentInElement(): void
    {
        $element = new Element('foo');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('label');
        $markup = $this->helper->render($element);
    }

    public function testPassingElementToRenderGeneratesButtonMarkup(): void
    {
        $element = new Element('foo');
        $element->setLabel('{button_content}');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('>{button_content}<', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('</button>', $markup);
    }

    public function testPassingElementAndContentToRenderUsesContent(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element, '{button_content}');
        $this->assertStringContainsString('>{button_content}<', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('</button>', $markup);
    }

    public function testCallingFromViewHelperCanHandleOpenTagAndCloseTag(): void
    {
        $helper = $this->helper;
        $markup = $helper()->openTag();
        $this->assertEquals('<button>', $markup);
        $markup = $helper()->closeTag();
        $this->assertEquals('</button>', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element, '{button_content}');
        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('>{button_content}<', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testDoesNotThrowExceptionIfNameIsZero(): void
    {
        $element = new Element(0);
        $markup  = $this->helper->__invoke($element, '{button_content}');
        $this->assertStringContainsString('name="0"', $markup);
    }

    public function testCanTranslateContent(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');

        $mockTranslator = $this->createMock(Translator::class);
        $mockTranslator->expects($this->once())
            ->method('translate')
            ->willReturn('translated content');

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        $this->assertStringContainsString('>translated content<', $markup);
    }

    public function testCanTranslateButtonContentParameter(): void
    {
        $element = new Element('foo');

        $mockTranslator = $this->createMock(Translator::class);
        $mockTranslator->expects($this->once())
            ->method('translate')
            ->willReturn('translated content');

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element, 'translate me');
        $this->assertStringContainsString('>translated content<', $markup);
    }

    public function testTranslatorMethods(): void
    {
        $translatorMock = $this->createMock(Translator::class);
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }

    public function testLabelIsEscapedByDefault(): void
    {
        $element = new Element('foo');
        $element->setLabel('<strong>Click me</strong>');
        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression(
            '#<button([^>]*)>&lt;strong&gt;Click me&lt;/strong&gt;<\/button>#',
            $markup
        );
    }

    public function testCanDisableLabelHtmlEscape(): void
    {
        $element = new Element('foo');
        $element->setLabel('<strong>Click me</strong>');
        $element->setLabelOptions(['disable_html_escape' => true]);
        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression('#<button([^>]*)><strong>Click me</strong></button>#', $markup);
    }
}
