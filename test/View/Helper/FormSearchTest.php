<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormSearch as FormSearchHelper;

use function sprintf;

class FormSearchTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormSearchHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesTextInputTagWithElement()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="search"', $markup);
    }

    public function testGeneratesTextInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="search"', $markup);
    }

    public function validAttributes()
    {
        return [
            'name'           => ['name', 'assertStringContainsString'],
            'accept'         => ['accept', 'assertStringNotContainsString'],
            'alt'            => ['alt', 'assertStringNotContainsString'],
            'autocomplete'   => ['autocomplete', 'assertStringContainsString'],
            'autofocus'      => ['autofocus', 'assertStringContainsString'],
            'checked'        => ['checked', 'assertStringNotContainsString'],
            'dirname'        => ['dirname', 'assertStringContainsString'],
            'disabled'       => ['disabled', 'assertStringContainsString'],
            'form'           => ['form', 'assertStringContainsString'],
            'formaction'     => ['formaction', 'assertStringNotContainsString'],
            'formenctype'    => ['formenctype', 'assertStringNotContainsString'],
            'formmethod'     => ['formmethod', 'assertStringNotContainsString'],
            'formnovalidate' => ['formnovalidate', 'assertStringNotContainsString'],
            'formtarget'     => ['formtarget', 'assertStringNotContainsString'],
            'height'         => ['height', 'assertStringNotContainsString'],
            'list'           => ['list', 'assertStringContainsString'],
            'max'            => ['max', 'assertStringNotContainsString'],
            'maxlength'      => ['maxlength', 'assertStringContainsString'],
            'min'            => ['min', 'assertStringNotContainsString'],
            'minlength'      => ['minlength', 'assertStringContainsString'],
            'multiple'       => ['multiple', 'assertStringNotContainsString'],
            'pattern'        => ['pattern', 'assertStringContainsString'],
            'placeholder'    => ['placeholder', 'assertStringContainsString'],
            'readonly'       => ['readonly', 'assertStringContainsString'],
            'required'       => ['required', 'assertStringContainsString'],
            'size'           => ['size', 'assertStringContainsString'],
            'src'            => ['src', 'assertStringNotContainsString'],
            'step'           => ['step', 'assertStringNotContainsString'],
            'value'          => ['value', 'assertStringContainsString'],
            'width'          => ['width', 'assertStringNotContainsString'],
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
            'minlength'          => 'value',
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

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<input', $markup);
        $this->assertStringContainsString('name="foo"', $markup);
        $this->assertStringContainsString('type="search"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
