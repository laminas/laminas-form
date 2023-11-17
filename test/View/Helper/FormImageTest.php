<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormImage as FormImageHelper;

use function sprintf;

/**
 * @property FormImageHelper $helper
 */
final class FormImageTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormImageHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new Element();
        $element->setAttribute('src', 'foo.png');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testRaisesExceptionWhenSrcIsNotPresentInElement(): void
    {
        $element = new Element('foo');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('src');
        $this->helper->render($element);
    }

    public function testGeneratesImageInputTagWithElement(): void
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="image"', $markup);
        self::assertStringContainsString('src="foo.png"', $markup);
    }

    public function testGeneratesImageInputTagRegardlessOfElementType(): void
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $element->setAttribute('type', 'email');
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="image"', $markup);
        self::assertStringContainsString('src="foo.png"', $markup);
    }

    public static function validAttributes(): array
    {
        return [
            ['name', 'assertStringContainsString'],
            ['accept', 'assertStringNotContainsString'],
            ['alt', 'assertStringContainsString'],
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
            ['height', 'assertStringContainsString'],
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
            ['src', 'assertStringContainsString'],
            ['step', 'assertStringNotContainsString'],
            ['value', 'assertStringNotContainsString'],
            ['width', 'assertStringContainsString'],
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
        $markup  = $this->helper->render($element);
        $expect  = match ($attribute) {
            'value' => sprintf('%s="%s"', $attribute, (string) $element->getValue()),
            default => sprintf('%s="%s"', $attribute, (string) $element->getAttribute($attribute)),
        };
        $this->$assertion($expect, $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $markup = $this->helper->__invoke($element);
        self::assertStringContainsString('<input', $markup);
        self::assertStringContainsString('name="foo"', $markup);
        self::assertStringContainsString('type="image"', $markup);
        self::assertStringContainsString('src="foo.png"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }
}
