<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormMultiCheckbox as FormMultiCheckboxHelper;
use Laminas\I18n\Translator\Translator;

use function sprintf;
use function substr_count;

/**
 * @property FormMultiCheckboxHelper $helper
 */
final class FormMultiCheckboxTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormMultiCheckboxHelper();
        parent::setUp();
    }

    public function getElement(): MultiCheckboxElement
    {
        $element = new MultiCheckboxElement('foo');
        $options = [
            'value1' => 'This is the first label',
            'value2' => 'This is the second label',
            'value3' => 'This is the third label',
        ];
        $element->setValueOptions($options);
        return $element;
    }

    public function getElementWithOptionSpec(): MultiCheckboxElement
    {
        $element = new MultiCheckboxElement('foo');
        $options = [
            'value1' => 'This is the first label',
            1        => [
                'value'            => 'value2',
                'label'            => 'This is the second label (overridden)',
                'disabled'         => false,
                'label_attributes' => ['class' => 'label-class'],
                'attributes'       => ['class' => 'input-class'],
            ],
            'value3' => 'This is the third label',
        ];
        $element->setValueOptions($options);
        return $element;
    }

    public function testUsesOptionsAttributeToGenerateCheckBoxes(): void
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        self::assertEquals(3, substr_count($markup, 'name="foo'));
        self::assertEquals(3, substr_count($markup, 'type="checkbox"'));
        self::assertEquals(3, substr_count($markup, '<input'));
        self::assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            self::assertStringContainsString(sprintf('>%s</label>', $label), $markup);
            self::assertStringContainsString(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesOptionsAttributeWithOptionSpecToGenerateCheckBoxes(): void
    {
        $element = $this->getElementWithOptionSpec();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        self::assertEquals(3, substr_count($markup, 'name="foo'));
        self::assertEquals(3, substr_count($markup, 'type="checkbox"'));
        self::assertEquals(3, substr_count($markup, '<input'));
        self::assertEquals(3, substr_count($markup, '<label'));

        self::assertStringContainsString(
            sprintf('>%s</label>', 'This is the first label'),
            $markup
        );
        self::assertStringContainsString(sprintf('value="%s"', 'value1'), $markup);

        self::assertStringContainsString(
            sprintf('>%s</label>', 'This is the second label (overridden)'),
            $markup
        );
        self::assertStringContainsString(sprintf('value="%s"', 'value2'), $markup);
        self::assertEquals(1, substr_count($markup, 'class="label-class"'));
        self::assertEquals(1, substr_count($markup, 'class="input-class"'));

        self::assertStringContainsString(
            sprintf('>%s</label>', 'This is the third label'),
            $markup
        );
        self::assertStringContainsString(sprintf('value="%s"', 'value3'), $markup);
    }

    public function testGenerateCheckBoxesAndHiddenElement(): void
    {
        $element = $this->getElement();
        $element->setUseHiddenElement(true);
        $element->setUncheckedValue('none');
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        self::assertEquals(4, substr_count($markup, 'name="foo'));
        self::assertEquals(1, substr_count($markup, 'type="hidden"'));
        self::assertEquals(1, substr_count($markup, 'value="none"'));
        self::assertEquals(3, substr_count($markup, 'type="checkbox"'));
        self::assertEquals(4, substr_count($markup, '<input'));
        self::assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            self::assertStringContainsString(sprintf('>%s</label>', $label), $markup);
            self::assertStringContainsString(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesElementValueToDetermineCheckboxStatus(): void
    {
        $element = $this->getElement();
        $element->setAttribute('value', ['value1', 'value3']);
        $markup = $this->helper->render($element);

        self::assertMatchesRegularExpression('#value="value1"\s+checked="checked"#', $markup);
        self::assertDoesNotMatchRegularExpression('#value="value2"\s+checked="checked"#', $markup);
        self::assertMatchesRegularExpression('#value="value3"\s+checked="checked"#', $markup);
    }

    public function testAllowsSpecifyingSeparator(): void
    {
        $element = $this->getElement();
        $this->helper->setSeparator('<br />');
        $markup = $this->helper->render($element);
        self::assertEquals(2, substr_count($markup, '<br />'));
    }

    public function testAllowsSpecifyingLabelPosition(): void
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $this->helper->setLabelPosition(FormMultiCheckboxHelper::LABEL_PREPEND);
        $markup = $this->helper->render($element);

        self::assertEquals(3, substr_count($markup, 'name="foo'));
        self::assertEquals(3, substr_count($markup, 'type="checkbox"'));
        self::assertEquals(3, substr_count($markup, '<input'));
        self::assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            self::assertStringContainsString(sprintf('<label>%s<', $label), $markup);
        }
    }

    public function testAllowsSpecifyingLabelAttributes(): void
    {
        $element = $this->getElement();

        $markup = $this->helper
            ->setLabelAttributes(['class' => 'checkbox'])
            ->render($element);

        self::assertEquals(3, substr_count($markup, '<label class="checkbox"'));
    }

    public function testAllowsSpecifyingLabelAttributesInElementAttributes(): void
    {
        $element = $this->getElement();
        $element->setLabelAttributes(['class' => 'checkbox']);
        $markup = $this->helper->render($element);

        self::assertEquals(3, substr_count($markup, '<label class="checkbox"'));
    }

    public function testIdShouldNotBeRenderedForEachRadio(): void
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        self::assertLessThanOrEqual(1, substr_count($markup, 'id="foo"'));
    }

    public function testIdShouldBeRenderedOnceIfProvided(): void
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        self::assertEquals(1, substr_count($markup, 'id="foo"'));
    }

    public function testNameShouldHaveBracketsAppended(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        self::assertStringContainsString('foo&#x5B;&#x5D;', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $element = $this->getElement();
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testEnsureUseHiddenElementMethodExists(): void
    {
        $element = new MultiCheckboxElement();
        $element->setName('codeType');
        $element->setOptions(['label' => 'Code Type']);
        $element->setAttributes([
            'type'  => 'radio',
            'value' => ['markdown'],
        ]);
        $element->setValueOptions([
            'Markdown' => 'markdown',
            'HTML'     => 'html',
            'Wiki'     => 'wiki',
        ]);

        $markup = $this->helper->render($element);
        self::assertStringNotContainsString('type="hidden"', $markup);
        // Lack of error also indicates this test passes
    }

    public function testCanTranslateContent(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

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

    public function testRenderInputNotSelectElementRaisesException(): void
    {
        $element = new Element\Text('foo');
        $this->expectException(InvalidArgumentException::class);
        $this->helper->render($element);
    }

    public function testRenderElementWithNoNameRaisesException(): void
    {
        $element = new MultiCheckboxElement(null);

        $this->expectException(DomainException::class);
        $this->helper->render($element);
    }

    public function testCanMarkSingleOptionAsSelected(): void
    {
        $element = new MultiCheckboxElement('foo');
        $options = [
            'value1' => 'This is the first label',
            1        => [
                'value'            => 'value2',
                'label'            => 'This is the second label (overridden)',
                'disabled'         => false,
                'selected'         => true,
                'label_attributes' => ['class' => 'label-class'],
                'attributes'       => ['class' => 'input-class'],
            ],
            'value3' => 'This is the third label',
        ];
        $element->setValueOptions($options);

        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#class="input-class" value="value2" checked="checked"#', $markup);
        self::assertDoesNotMatchRegularExpression('#class="input-class" value="value1" checked="checked"#', $markup);
        self::assertDoesNotMatchRegularExpression('#class="input-class" value="value3" checked="checked"#', $markup);
    }

    public function testInvokeSetLabelPositionToAppend(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $this->helper->__invoke($element, 'append');

        self::assertSame('append', $this->helper->getLabelPosition());
    }

    public function testSetLabelAttributes(): void
    {
        $this->helper->setLabelAttributes(['foo', 'bar']);
        self::assertEquals([0 => 'foo', 1 => 'bar'], $this->helper->getLabelAttributes());
    }

    public function testGetUseHiddenElementReturnsDefaultFalse(): void
    {
        $hiddenElement = $this->helper->getUseHiddenElement();
        self::assertFalse($hiddenElement);
    }

    public function testGetUseHiddenElementSetToTrue(): void
    {
        $this->helper->setUseHiddenElement(true);
        $hiddenElement = $this->helper->getUseHiddenElement();
        self::assertTrue($hiddenElement);
    }

    public function testGetUncheckedValueReturnsDefaultEmptyString(): void
    {
        $uncheckedValue = $this->helper->getUncheckedValue();
        self::assertNull($uncheckedValue);
    }

    public function testGetUncheckedValueSetToFoo(): void
    {
        $this->helper->setUncheckedValue('foo');
        $uncheckedValue = $this->helper->getUncheckedValue();
        self::assertSame('foo', $uncheckedValue);
    }

    public function testGetDisableAttributeReturnTrue(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setAttribute('disabled', 'true');
        self::assertSame('true', $element->getAttribute('disabled'));
    }

    public function testGetSelectedAttributeReturnTrue(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setAttribute('selected', 'true');
        self::assertSame('true', $element->getAttribute('selected'));
    }

    public function testGetDisableAttributeForGroupReturnTrue(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setAttribute('disabled', 'true');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#disabled="disabled" value="value1"#', $markup);
    }

    public function testGetSelectedAttributeForGroupReturnTrue(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setAttribute('selected', 'true');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#value="value1" checked="checked"#', $markup);
    }

    public function testDisableEscapeHtmlHelper(): void
    {
        $element = new MultiCheckboxElement('foo');
        $element->setLabelOptions([
            'disable_html_escape' => true,
        ]);
        $element->setValueOptions([
            [
                'label' => '<span>label1</span>',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#<span>label1</span>#', $markup);
    }

    /**
     * @group issue-6649
     * @group issue-6655
     */
    public function testRenderWithoutValueOptions(): void
    {
        $element = new MultiCheckboxElement('foo');

        self::assertEmpty($this->helper->render($element));
    }

    public function testRendersZeroAsUncheckedValueOfElement(): void
    {
        $element = $this->getElement();
        $element->setUseHiddenElement(true);

        $markup = $this->helper->render($element);

        $expectedElement = '<input type="hidden" name="foo" value="">';
        self::assertStringContainsString($expectedElement, $markup);

        $element->setUncheckedValue('');

        $markup = $this->helper->render($element);

        $expectedElement = '<input type="hidden" name="foo" value="">';
        self::assertStringContainsString($expectedElement, $markup);

        $element->setUncheckedValue('0');

        $markup = $this->helper->render($element);

        $expectedElement = '<input type="hidden" name="foo" value="0">';
        self::assertStringContainsString($expectedElement, $markup);
    }
}
