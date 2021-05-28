<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormCheckbox as FormCheckboxHelper;

/**
 * @property FormCheckboxHelper $helper
 */
final class FormCheckboxTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormCheckboxHelper();
        parent::setUp();
    }

    public function getElement(): Element\Checkbox
    {
        $element = new Element\Checkbox('foo');
        $options = [
            'checked_value'   => 'checked',
            'unchecked_value' => 'unchecked',
        ];
        $element->setOptions($options);
        return $element;
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new Element\Checkbox();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testUsesOptionsAttributeToGenerateCheckedAndUnCheckedValues(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        $this->assertStringContainsString('type="checkbox"', $markup);
        $this->assertStringContainsString('value="checked"', $markup);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('value="unchecked"', $markup);
    }

    public function testUsesElementValueToDetermineCheckboxStatus(): void
    {
        $element = $this->getElement();
        $element->setValue('checked');
        $markup = $this->helper->render($element);

        $this->assertMatchesRegularExpression('#value="checked"\s+checked="checked"#', $markup);
        $this->assertDoesNotMatchRegularExpression('#value="unchecked"\s+checked="checked"#', $markup);
    }

    public function testNoOptionsAttributeCreatesDefaultCheckedAndUncheckedValues(): void
    {
        $element = new Element\Checkbox('foo');
        $markup  = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#type="checkbox".*?(value="1")#', $markup);
        $this->assertMatchesRegularExpression('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testSetUseHiddenElementAttributeDoesNotRenderHiddenInput(): void
    {
        $element = new Element\Checkbox('foo');
        $element->setUseHiddenElement(false);
        $markup = $this->helper->render($element);
        $this->assertMatchesRegularExpression('#type="checkbox".*?(value="1")#', $markup);
        $this->assertDoesNotMatchRegularExpression('#type="hidden"\s+name="foo"\s+value="0"#', $markup);
    }

    public function testDoesNotThrowExceptionIfNameIsZero(): void
    {
        $element = new Element\Checkbox(0);
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('name="0"', $markup);
    }

    /**
     * @group Laminas-457
     */
    public function testBaseElementType(): void
    {
        $element = new Element('foo');
        $this->expectException(InvalidArgumentException::class);
        $markup = $this->helper->render($element);
    }

    /**
     * @group issue-7286
     */
    public function testDisabledOptionIssetOnHiddenElement(): void
    {
        $element = new Element\Checkbox('foo');
        $element->setUseHiddenElement(true);
        $element->setAttribute('disabled', true);

        $markup = $this->helper->__invoke($element);
        $this->assertMatchesRegularExpression('#type="hidden"[^>]?disabled="disabled"#', $markup);
    }
}
