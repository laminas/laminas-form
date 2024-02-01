<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\View\Helper\FormSelect as FormSelectHelper;
use Laminas\I18n\Translator\Translator;
use LaminasTest\Form\TestAsset\Identifier;

use function key;
use function sprintf;
use function substr_count;

/**
 * @property FormSelectHelper $helper
 */
final class FormSelectTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormSelectHelper();
        parent::setUp();
    }

    public function getElement(): SelectElement
    {
        $element = new SelectElement('foo');
        $options = [
            [
                'label' => 'This is the first label',
                'value' => 'value1',
            ],
            [
                'label' => 'This is the second label',
                'value' => 'value2',
            ],
            [
                'label'      => 'This is the third label',
                'value'      => 'value3',
                'attributes' => [
                    'class' => 'test-class',
                ],
            ],
        ];
        $element->setValueOptions($options);
        return $element;
    }

    public function testCreatesSelectWithOptionsFromAttribute(): void
    {
        $element = $this->getElement();
        $markup  = $this->helper->render($element);

        self::assertEquals(1, substr_count($markup, '<select'));
        self::assertEquals(1, substr_count($markup, '</select>'));
        self::assertEquals(3, substr_count($markup, '<option'));
        self::assertEquals(3, substr_count($markup, '</option>'));
        self::assertStringContainsString('>This is the first label<', $markup);
        self::assertStringContainsString('>This is the second label<', $markup);
        self::assertStringContainsString('>This is the third label<', $markup);
        self::assertStringContainsString('value="value1"', $markup);
        self::assertStringContainsString('value="value2"', $markup);
        self::assertStringContainsString('value="value3"', $markup);

        //Test class attribute on third option
        self::assertMatchesRegularExpression('#option .*?value="value3" class="test-class"#', $markup);
    }

    public function testCanMarkSingleOptionAsSelected(): void
    {
        $element = $this->getElement();
        $element->setAttribute('value', 'value2');

        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#option .*?value="value2" selected="selected"#', $markup);
        self::assertDoesNotMatchRegularExpression('#option .*?value="value1" selected="selected"#', $markup);
        self::assertDoesNotMatchRegularExpression('#option .*?value="value3" selected="selected"#', $markup);
    }

    public function testCanOnlyMarkSingleOptionAsSelectedIfMultipleAttributeIsDisabled(): void
    {
        $element = $this->getElement();
        $element->setValue(['value1', 'value2']);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('multiple');
        $this->helper->render($element);
    }

    public function testCanMarkManyOptionsAsSelectedIfMultipleAttributeIsEnabled(): void
    {
        $element = $this->getElement();
        $element->setAttribute('multiple', true);
        $element->setValue(['value1', 'value2']);
        $markup = $this->helper->render($element);

        self::assertMatchesRegularExpression('#select .*?multiple="multiple"#', $markup);
        self::assertMatchesRegularExpression('#option .*?value="value1" selected="selected"#', $markup);
        self::assertMatchesRegularExpression('#option .*?value="value2" selected="selected"#', $markup);
        self::assertDoesNotMatchRegularExpression('#option .*?value="value3" selected="selected"#', $markup);
    }

    public function testCanMarkOptionsAsDisabled(): void
    {
        $element                = $this->getElement();
        $options                = $element->getValueOptions();
        $options[1]['disabled'] = true;
        $element->setValueOptions($options);

        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#option .*?value="value2" .*?disabled="disabled"#', $markup);
    }

    public function testCanMarkOptionsAsSelected(): void
    {
        $element                = $this->getElement();
        $options                = $element->getValueOptions();
        $options[1]['selected'] = true;
        $element->setValueOptions($options);

        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#option .*?value="value2" .*?selected="selected"#', $markup);
    }

    public function testOptgroupsAreCreatedWhenAnOptionHasAnOptionsKey(): void
    {
        $element               = $this->getElement();
        $options               = $element->getValueOptions();
        $options[1]['options'] = [
            [
                'label' => 'foo',
                'value' => 'bar',
            ],
        ];
        $element->setValueOptions($options);

        $markup = $this->helper->render($element);
        // @codingStandardsIgnoreStart
        self::assertMatchesRegularExpression('#optgroup[^>]*?label="This\&\#x20\;is\&\#x20\;the\&\#x20\;second\&\#x20\;label"[^>]*>\s*<option[^>]*?value="bar"[^>]*?>foo.*?</optgroup>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testCanDisableAnOptgroup(): void
    {
        $element                = $this->getElement();
        $options                = $element->getValueOptions();
        $options[1]['disabled'] = true;
        $options[1]['options']  = [
            [
                'label' => 'foo',
                'value' => 'bar',
            ],
        ];
        $element->setValueOptions($options);

        $markup = $this->helper->render($element);
        // @codingStandardsIgnoreStart
        self::assertMatchesRegularExpression('#optgroup .*?label="This\&\#x20\;is\&\#x20\;the\&\#x20\;second\&\#x20\;label"[^>]*?disabled="disabled"[^>]*?>\s*<option[^>]*?value="bar"[^>]*?>foo.*?</optgroup>#', $markup);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @group Laminas-290
     */
    public function testFalseDisabledValueWillNotRenderOptionsWithDisabledAttribute(): void
    {
        $element = $this->getElement();
        $element->setAttribute('disabled', false);
        $markup = $this->helper->render($element);

        self::assertStringNotContainsString('disabled', $markup);
    }

    /**
     * @group Laminas-290
     */
    public function testOmittingDisabledValueWillNotRenderOptionsWithDisabledAttribute(): void
    {
        $element = $this->getElement();
        $element->setAttribute('type', 'select');
        $markup = $this->helper->render($element);

        self::assertStringNotContainsString('disabled', $markup);
    }

    public function testNameShouldHaveArrayNotationWhenMultipleIsSpecified(): void
    {
        $element = $this->getElement();
        $element->setAttribute('multiple', true);
        $element->setValue(['value1', 'value2']);
        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#<select[^>]*?(name="foo\&\#x5B\;\&\#x5D\;")#', $markup);
    }

    public static function getScalarOptionsDataProvider(): array
    {
        return [
            [['value' => 'string']],
            [[1 => 'int']],
            [[-1 => 'int-neg']],
            [[0x1A => 'hex']],
            [[0123 => 'oct']],
        ];
    }

    /**
     * @group Laminas-338
     * @dataProvider getScalarOptionsDataProvider
     */
    public function testScalarOptionValues(array $options): void
    {
        $element = new SelectElement('foo');
        $element->setValueOptions($options);
        $markup = $this->helper->render($element);
        $value  = key($options);
        self::assertMatchesRegularExpression(sprintf('#option .*?value="%s"#', (string) $value), $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $element = $this->getElement();
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCanTranslateContent(): void
    {
        $element = new SelectElement('foo');
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

    public function testCanTranslateOptGroupLabel(): void
    {
        $element = new SelectElement('test');
        $element->setValueOptions([
            'optgroup' => [
                'label'   => 'translate me',
                'options' => [
                    '0' => 'foo',
                    '1' => 'bar',
                ],
            ],
        ]);

        $mockTranslator = $this->createMock(Translator::class);
        $mockTranslator->expects($this->exactly(3))
            ->method('translate')
            ->willReturnMap([
                ['translate me', 'default', null, 'translated label'],
                ['foo', 'default', null, 'translated foo'],
                ['bar', 'default', null, 'translated bar'],
            ]);

        $this->helper->setTranslator($mockTranslator);
        self::assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);

        self::assertStringContainsString('label="translated&#x20;label"', $markup);
        self::assertStringContainsString('>translated foo<', $markup);
        self::assertStringContainsString('>translated bar<', $markup);
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

    public function testDoesNotThrowExceptionIfNameIsZero(): void
    {
        $element = $this->getElement();
        $element->setName('0');

        $this->helper->__invoke($element);
        $markup = $this->helper->__invoke($element);
        self::assertStringContainsString('name="0"', $markup);
    }

    public function testCanCreateEmptyOption(): void
    {
        $element = new SelectElement('foo');
        $element->setEmptyOption('empty');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<option value="">empty</option>', $markup);
    }

    public function testCanCreateEmptyOptionWithEmptyString(): void
    {
        $element = new SelectElement('foo');
        $element->setEmptyOption('');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testDoesNotRenderEmptyOptionByDefault(): void
    {
        $element = new SelectElement('foo');
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

        self::assertStringNotContainsString('<option value=""></option>', $markup);
    }

    public function testNullEmptyOptionDoesNotRenderEmptyOption(): void
    {
        $element = new SelectElement('foo');
        $element->setEmptyOption(null);
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

        self::assertStringNotContainsString('<option value=""></option>', $markup);
    }

    public function testCanMarkOptionsAsSelectedWhenEmptyOptionOrZeroValueSelected(): void
    {
        $element = new SelectElement('foo');
        $element->setEmptyOption('empty');
        $element->setValueOptions([
            0 => 'label0',
            1 => 'label1',
        ]);

        $element->setValue('');
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<option value="" selected="selected">empty</option>', $markup);
        self::assertStringContainsString('<option value="0">label0</option>', $markup);

        $element->setValue('0');
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<option value="">empty</option>', $markup);
        self::assertStringContainsString('<option value="0" selected="selected">label0</option>', $markup);
    }

    public function testHiddenElementWhenAttributeMultipleIsSet(): void
    {
        $element = new SelectElement('foo');
        $element->setUseHiddenElement(true);
        $element->setUnselectedValue('empty');

        $markup = $this->helper->render($element);
        self::assertStringContainsString('<input type="hidden" name="foo" value="empty"><select', $markup);
    }

    public function testHiddenElementWhenNoRenderer(): void
    {
        $element = new SelectElement('foo');
        $element->setUseHiddenElement(true);
        $element->setUnselectedValue('empty');
        $helper = new FormSelectHelper();
        $markup = $helper->render($element);
        self::assertStringContainsString('<input type="hidden" name="foo" value="empty"><select', $markup);
    }

    public function testRenderInputNotSelectElementRaisesException(): void
    {
        $element = new Element\Text('foo');
        $this->expectException(InvalidArgumentException::class);
        $this->helper->render($element);
    }

    public function testRenderElementWithNoNameRaisesException(): void
    {
        $element = new SelectElement();

        $this->expectException(DomainException::class);
        $this->helper->render($element);
    }

    public function getElementWithObjectIdentifiers(): SelectElement
    {
        $element = new SelectElement('foo');
        $options = [
            [
                'label' => 'This is the first label',
                'value' => new Identifier(42),
            ],
            [
                'label' => 'This is the second label',
                'value' => new Identifier(43),
            ],
        ];
        $element->setValueOptions($options);
        return $element;
    }

    public function testRenderElementWithObjectIdentifiers(): void
    {
        $element = $this->getElementWithObjectIdentifiers();
        $element->setValue(new Identifier(42));

        $markup = $this->helper->render($element);
        self::assertMatchesRegularExpression('#option .*?value="42" selected="selected"#', $markup);
        self::assertDoesNotMatchRegularExpression('#option .*?value="43" selected="selected"#', $markup);
    }

    public function testComparisonOfSelectedValuesIsPerformedInStrictMode(): void
    {
        $select = new SelectElement('language');
        $select->setLabel('Which is your mother tongue?');
        $select->setAttribute('multiple', true);
        $select->setValueOptions([
            '1.1'  => 'French',
            '1.2'  => 'English',
            '1.10' => 'Japanese',
            '1.20' => 'Chinese',
        ]);
        $select->setValue(['1.1']);
        self::assertEquals(['1.1'], $select->getValue());

        $markup = $this->helper->render($select);

        self::assertMatchesRegularExpression('{value="1.1" selected="selected"}i', $markup);
        self::assertDoesNotMatchRegularExpression('{value="1.2" selected="selected"}i', $markup);
        self::assertDoesNotMatchRegularExpression('{value="1.10" selected="selected"}i', $markup);
        self::assertDoesNotMatchRegularExpression('{value="1.20" selected="selected"}i', $markup);
    }

    public function testArrayEmptyOptionRendersOption(): void
    {
        $element = new SelectElement('foo');
        $element->setEmptyOption([
            'value'    => null,
            'label'    => 'Select...',
            'disabled' => 'disabled',
        ]);
        $element->setValueOptions([
            [
                'label' => 'label1',
                'value' => 'value1',
            ],
        ]);
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<option value="" disabled="disabled">Select...</option>', $markup);
    }
}
