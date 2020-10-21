<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormCheckbox as FormCheckboxHelper;

class FormCheckboxTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormCheckboxHelper();
        parent::setUp();
    }

    public function getElement()
    {
        $element = new Element\Checkbox('foo');
        $options = [
            'checked_value'   => 'checked',
            'unchecked_value' => 'unchecked',
        ];
        $element->setOptions($options);
        return $element;
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element\Checkbox();
        $this->expectException('Laminas\Form\Exception\DomainException');
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testUsesOptionsAttributeToGenerateCheckedAndUnCheckedValues()
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        $this->assertStringContainsString('type="checkbox"', $markup);
        $this->assertStringContainsString('value="checked"', $markup);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('value="unchecked"', $markup);
    }

    public function testUsesElementValueToDetermineCheckboxStatus()
    {
        $element = $this->getElement();
        $element->setValue('checked');
        $markup  = $this->helper->render($element);

        $this->assertMatchesRegularExpression('#value="checked"\s+checked="checked"#', $markup);
        $this->assertDoesNotMatchRegularExpression('#value="unchecked"\s+checked="checked"#', $markup);
    }

    public function testNoOptionsAttributeCreatesDefaultCheckedAndUncheckedValues()
    {
        $element = new Element\Checkbox('foo');
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#type="checkbox".*?(value="1")#', $markup);
        $this->assertMatchesRegularExpression('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testSetUseHiddenElementAttributeDoesNotRenderHiddenInput()
    {
        $element = new Element\Checkbox('foo');
        $element->setUseHiddenElement(false);
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#type="checkbox".*?(value="1")#', $markup);
        $this->assertDoesNotMatchRegularExpression('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testDoesNotThrowExceptionIfNameIsZero()
    {
        $element = new Element\Checkbox(0);
        $markup = $this->helper->__invoke($element);
        $this->assertStringContainsString('name="0"', $markup);
    }

    /**
     * @group Laminas-457
     */
    public function testBaseElementType()
    {
        $element = new Element('foo');
        $this->expectException('Laminas\Form\Exception\InvalidArgumentException');
        $markup = $this->helper->render($element);
    }

    /**
     * @group laminas7286
     */
    public function testDisabledOptionIssetOnHiddenElement()
    {
        $element = new Element\Checkbox('foo');
        $element->setUseHiddenElement(true);
        $element->setAttribute('disabled', true);

        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression('#type="hidden"[^>]?disabled="disabled"#', $markup);
    }
}
