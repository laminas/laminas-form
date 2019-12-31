<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormWeek as FormWeekHelper;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTest
 */
class FormWeekTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormWeekHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Laminas\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesInputTagWithElement()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="week"', $markup);
    }

    public function testGeneratesInputTagRegardlessOfElementType()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="week"', $markup);
    }

    public function validAttributes()
    {
        return array(
            array('name',           'assertContains'),
            array('accept',         'assertNotContains'),
            array('alt',            'assertNotContains'),
            array('autocomplete',   'assertContains'),
            array('autofocus',      'assertContains'),
            array('checked',        'assertNotContains'),
            array('dirname',        'assertNotContains'),
            array('disabled',       'assertContains'),
            array('form',           'assertContains'),
            array('formaction',     'assertNotContains'),
            array('formenctype',    'assertNotContains'),
            array('formmethod',     'assertNotContains'),
            array('formnovalidate', 'assertNotContains'),
            array('formtarget',     'assertNotContains'),
            array('height',         'assertNotContains'),
            array('list',           'assertContains'),
            array('max',            'assertContains'),
            array('maxlength',      'assertNotContains'),
            array('min',            'assertContains'),
            array('multiple',       'assertNotContains'),
            array('pattern',        'assertNotContains'),
            array('placeholder',    'assertNotContains'),
            array('readonly',       'assertContains'),
            array('required',       'assertContains'),
            array('size',           'assertNotContains'),
            array('src',            'assertNotContains'),
            array('step',           'assertContains'),
            array('value',          'assertContains'),
            array('width',          'assertNotContains'),
        );
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes(array(
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
        ));
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
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="week"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
