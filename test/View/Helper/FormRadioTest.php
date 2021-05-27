<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element\Radio as RadioElement;
use Laminas\Form\View\Helper\FormRadio as FormRadioHelper;
use Laminas\I18n\Translator\Translator;

use function sprintf;
use function substr_count;

/**
 * @property FormRadioHelper $helper
 */
class FormRadioTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormRadioHelper();
        parent::setUp();
    }

    public function getElement(): RadioElement
    {
        $element = new RadioElement('foo');
        $options = [
            'value1' => 'This is the first label',
            'value2' => 'This is the second label',
            'value3' => 'This is the third label',
        ];
        $element->setValueOptions($options);
        return $element;
    }

    public function getElementWithOptionSpec(): RadioElement
    {
        $element = new RadioElement('foo');
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

    public function testUsesOptionsAttributeToGenerateRadioOptions(): void
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertStringContainsString(sprintf('>%s</label>', $label), $markup);
            $this->assertStringContainsString(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesOptionsAttributeWithOptionSpecToGenerateRadioOptions(): void
    {
        $element = $this->getElementWithOptionSpec();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        $this->assertStringContainsString(
            sprintf('>%s</label>', 'This is the first label'),
            $markup
        );
        $this->assertStringContainsString(sprintf('value="%s"', 'value1'), $markup);

        $this->assertStringContainsString(
            sprintf('>%s</label>', 'This is the second label (overridden)'),
            $markup
        );
        $this->assertStringContainsString(sprintf('value="%s"', 'value2'), $markup);
        $this->assertEquals(1, substr_count($markup, 'class="label-class"'));
        $this->assertEquals(1, substr_count($markup, 'class="input-class"'));

        $this->assertStringContainsString(
            sprintf('>%s</label>', 'This is the third label'),
            $markup
        );
        $this->assertStringContainsString(sprintf('value="%s"', 'value3'), $markup);
    }

    public function testGenerateRadioOptionsAndHiddenElement(): void
    {
        $element = $this->getElement();
        $element->setUseHiddenElement(true);
        $element->setUncheckedValue('none');
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertEquals(4, substr_count($markup, 'name="foo'));
        $this->assertEquals(1, substr_count($markup, 'type="hidden"'));
        $this->assertEquals(1, substr_count($markup, 'value="none"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(4, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertStringContainsString(sprintf('>%s</label>', $label), $markup);
            $this->assertStringContainsString(sprintf('value="%s"', $value), $markup);
        }
    }

    public function testUsesElementValueToDetermineRadioStatus(): void
    {
        $element = $this->getElement();
        $element->setAttribute('value', ['value1', 'value3']);
        $markup = $this->helper->render($element);

        $this->assertMatchesRegularExpression('#value="value1"\s+checked="checked"#', $markup);
        $this->assertDoesNotMatchRegularExpression('#value="value2"\s+checked="checked"#', $markup);
        $this->assertMatchesRegularExpression('#value="value3"\s+checked="checked"#', $markup);
    }

    public function testAllowsSpecifyingSeparator(): void
    {
        $element = $this->getElement();
        $this->helper->setSeparator('<br />');
        $markup = $this->helper->render($element);
        $this->assertEquals(2, substr_count($markup, '<br />'));
    }

    public function testAllowsSpecifyingLabelPosition(): void
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $this->helper->setLabelPosition(FormRadioHelper::LABEL_PREPEND);
        $markup = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, 'name="foo"'));
        $this->assertEquals(3, substr_count($markup, 'type="radio"'));
        $this->assertEquals(3, substr_count($markup, '<input'));
        $this->assertEquals(3, substr_count($markup, '<label'));

        foreach ($options as $value => $label) {
            $this->assertStringContainsString(sprintf('<label>%s<', $label), $markup);
        }
    }

    public function testDoesNotRenderCheckedAttributeIfNotPassed(): void
    {
        $element = $this->getElement();
        $options = $element->getValueOptions();
        $markup  = $this->helper->render($element);

        $this->assertStringNotContainsString('checked', $markup);
    }

    public function testAllowsSpecifyingLabelAttributes(): void
    {
        $element = $this->getElement();

        $markup = $this->helper
            ->setLabelAttributes(['class' => 'radio'])
            ->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="radio"'));
    }

    public function testAllowsSpecifyingLabelAttributesInElementAttributes(): void
    {
        $element = $this->getElement();
        $element->setLabelAttributes(['class' => 'radio']);

        $markup = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, '<label class="radio"'));
    }

    public function testIdShouldNotBeRenderedForEachRadio(): void
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        $this->assertLessThanOrEqual(1, substr_count($markup, 'id="foo"'));
    }

    public function testIdShouldBeRenderedOnceIfProvided(): void
    {
        $element = $this->getElement();
        $element->setAttribute('id', 'foo');
        $markup = $this->helper->render($element);
        $this->assertEquals(1, substr_count($markup, 'id="foo"'));
    }

    public function testNameShouldNotHaveBracketsAppended(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);
        $this->assertStringNotContainsString('foo[]', $markup);
    }

    public function testCanTranslateContent(): void
    {
        $element = new RadioElement('foo');
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
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);
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
}
