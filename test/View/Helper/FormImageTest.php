<?php

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormImage as FormImageHelper;

use function sprintf;

class FormImageTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormImageHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $element->setAttribute('src', 'foo.png');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testRaisesExceptionWhenSrcIsNotPresentInElement()
    {
        $element = new Element('foo');
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('src');
        $this->helper->render($element);
    }

    public function testGeneratesImageInputTagWithElement()
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="image"', $markup);
        $this->assertStringContainsString('src="foo.png"', $markup);
    }

    public function testGeneratesImageInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $element->setAttribute('type', 'email');
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="image"', $markup);
        $this->assertStringContainsString('src="foo.png"', $markup);
    }

    public function validAttributes(): array
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
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered(string $attribute, string $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            // Intentionally allowing fall-through
            case 'value':
                $expect = sprintf('%s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $element->setAttribute('src', 'foo.png');
        $markup = $this->helper->__invoke($element);
        $this->assertStringContainsString('<input', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('type="image"', $markup);
        $this->assertStringContainsString('src="foo.png"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
