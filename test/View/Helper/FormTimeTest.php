<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormTime as FormTimeHelper;

use function sprintf;

class FormTimeTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormTimeHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesInputTagWithElement()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="time"', $markup);
    }

    public function testGeneratesInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="time"', $markup);
    }

    public function validAttributes()
    {
        return [
            ['name',           'assertStringContainsString'],
            ['accept',         'assertStringNotContainsString'],
            ['alt',            'assertStringNotContainsString'],
            ['autocomplete',   'assertStringContainsString'],
            ['autofocus',      'assertStringContainsString'],
            ['checked',        'assertStringNotContainsString'],
            ['dirname',        'assertStringNotContainsString'],
            ['disabled',       'assertStringContainsString'],
            ['form',           'assertStringContainsString'],
            ['formaction',     'assertStringNotContainsString'],
            ['formenctype',    'assertStringNotContainsString'],
            ['formmethod',     'assertStringNotContainsString'],
            ['formnovalidate', 'assertStringNotContainsString'],
            ['formtarget',     'assertStringNotContainsString'],
            ['height',         'assertStringNotContainsString'],
            ['list',           'assertStringContainsString'],
            ['max',            'assertStringContainsString'],
            ['maxlength',      'assertStringNotContainsString'],
            ['min',            'assertStringContainsString'],
            ['multiple',       'assertStringNotContainsString'],
            ['pattern',        'assertStringNotContainsString'],
            ['placeholder',    'assertStringNotContainsString'],
            ['readonly',       'assertStringContainsString'],
            ['required',       'assertStringContainsString'],
            ['size',           'assertStringNotContainsString'],
            ['src',            'assertStringNotContainsString'],
            ['step',           'assertStringContainsString'],
            ['value',          'assertStringContainsString'],
            ['width',          'assertStringNotContainsString'],
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
        $this->assertStringContainsString('type="time"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
