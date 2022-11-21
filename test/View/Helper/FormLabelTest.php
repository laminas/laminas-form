<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormLabel as FormLabelHelper;
use Laminas\I18n\Translator\Translator;

use function sprintf;

/**
 * @property FormLabelHelper $helper
 */
final class FormLabelTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormLabelHelper();
        parent::setUp();
    }

    public function testCanEmitStartTagOnly(): void
    {
        $markup = $this->helper->openTag();
        self::assertEquals('<label>', $markup);
    }

    public function testOpenTagWithWrongElementRaisesException(): void
    {
        $element = new ArrayObject();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(ArrayObject::class);
        $this->helper->openTag($element);
    }

    public function testPassingArrayToOpenTagRendersAttributes(): void
    {
        $attributes = [
            'class'     => 'email-label',
            'data-type' => 'label',
        ];
        $markup     = $this->helper->openTag($attributes);

        foreach ($attributes as $key => $value) {
            self::assertStringContainsString(sprintf('%s="%s"', $key, $value), $markup);
        }
    }

    public function testCanEmitCloseTagOnly(): void
    {
        $markup = $this->helper->closeTag();
        self::assertEquals('</label>', $markup);
    }

    public function testPassingElementToOpenTagWillUseNameInForAttributeIfNoIdPresent(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->openTag($element);
        self::assertStringContainsString('for="foo"', $markup);
    }

    public function testPassingElementToOpenTagWillUseIdInForAttributeWhenPresent(): void
    {
        $element = new Element('foo');
        $element->setAttribute('id', 'bar');
        $markup = $this->helper->openTag($element);
        self::assertStringContainsString('for="bar"', $markup);
    }

    public function testPassingElementToInvokeWillRaiseExceptionIfNoNameOrIdAttributePresent(): void
    {
        $element = new Element();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('id');
        $markup = $this->helper->__invoke($element);
    }

    public function testPassingElementToInvokeWillRaiseExceptionIfNoLabelAttributePresent(): void
    {
        $element = new Element('foo');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('label');
        $markup = $this->helper->__invoke($element);
    }

    public function testPassingElementToInvokeGeneratesLabelMarkup(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element);
        self::assertStringContainsString('>The value for foo:<', $markup);
        self::assertStringContainsString('for="foo"', $markup);
        self::assertStringContainsString('<label', $markup);
        self::assertStringContainsString('</label>', $markup);
    }

    public function testPassingElementAndContentToInvokeUsesContentForLabel(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element, 'The value for foo:');
        self::assertStringContainsString('>The value for foo:<', $markup);
        self::assertStringContainsString('for="foo"', $markup);
        self::assertStringContainsString('<label', $markup);
        self::assertStringContainsString('</label>', $markup);
    }

    public function testPassingElementAndContentAndFlagToInvokeUsesLabelAttribute(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::PREPEND);
        self::assertStringContainsString('>The value for foo:<input', $markup);
        self::assertStringContainsString('for="foo"', $markup);
        self::assertStringContainsString('<label', $markup);
        self::assertStringContainsString('></label>', $markup);
        self::assertStringContainsString('<input type="text" id="foo" />', $markup);
    }

    public function testCanAppendLabelContentUsingFlagToInvoke(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
        self::assertStringContainsString('"foo" />The value for foo:</label>', $markup);
        self::assertStringContainsString('for="foo"', $markup);
        self::assertStringContainsString('<label', $markup);
        self::assertStringContainsString('><input type="text" id="foo" />', $markup);
    }

    public function testsetLabelAttributes(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $element->setLabelAttributes(['id' => 'bar']);
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
        self::assertStringContainsString('"foo" />The value for foo:</label>', $markup);
        self::assertStringContainsString('id="bar" for="foo"', $markup);
        self::assertStringContainsString('<label', $markup);
        self::assertStringContainsString('><input type="text" id="foo" />', $markup);
    }

    public function testPassingElementAndContextAndFlagToInvokeRaisesExceptionForMissingLabelAttribute(): void
    {
        $element = new Element('foo');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('label');
        $markup = $this->helper->__invoke($element, '<input type="text" id="foo" />', FormLabelHelper::APPEND);
    }

    public function testCallingFromViewHelperCanHandleOpenTagAndCloseTag(): void
    {
        $helper = $this->helper;
        $markup = $helper()->openTag();
        self::assertEquals('<label>', $markup);
        $markup = $helper()->closeTag();
        self::assertEquals('</label>', $markup);
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
        self::assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
        self::assertStringContainsString('>translated content<', $markup);
    }

    public function testTranslatorMethods(): void
    {
        $translatorMock = $this->createMock(Translator::class);
        $this->helper->setTranslator($translatorMock, 'foo');

        self::assertEquals($translatorMock, $this->helper->getTranslator());
        self::assertEquals('foo', $this->helper->getTranslatorTextDomain());
        self::assertTrue($this->helper->hasTranslator());
        self::assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        self::assertFalse($this->helper->isTranslatorEnabled());
    }

    public function testLabelIsEscapedByDefault(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value <a>for</a> foo:');
        $markup = $this->helper->__invoke($element);
        self::assertStringNotContainsString('<a>for</a>', $markup);
    }

    public function testCanDisableLabelHtmlEscape(): void
    {
        $element = new Element('foo');
        $element->setLabel('The value <a>for</a> foo:');
        $element->setLabelOptions(['disable_html_escape' => true]);
        $markup = $this->helper->__invoke($element);
        self::assertStringContainsString('<a>for</a>', $markup);
    }

    public function testAlwaysWrapIsDisabledByDefault(): void
    {
        $element = new Element('foo');
        self::assertEmpty($element->getLabelOption('always_wrap'));
    }

    public function testCanSetAlwaysWrap(): void
    {
        $element = new Element('foo');
        $element->setLabelOption('always_wrap', true);
        self::assertTrue($element->getLabelOption('always_wrap'));
    }
}
