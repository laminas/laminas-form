<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element\Collection;
use Laminas\Form\View\Helper\FormCollection as FormCollectionHelper;
use Laminas\I18n\Translator\Translator;
use Laminas\View\Helper\Doctype;
use LaminasTest\Form\TestAsset\CustomFieldsetHelper;
use LaminasTest\Form\TestAsset\CustomViewHelper;
use LaminasTest\Form\TestAsset\FormCollection;
use ReflectionMethod;
use RuntimeException;

/**
 * @property FormCollectionHelper $helper
 */
final class FormCollectionTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormCollectionHelper();
        parent::setUp();
    }

    public function getForm(): FormCollection
    {
        $form = new FormCollection();
        $form->prepare();

        return $form;
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCanGenerateTemplate(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->render($collection);
        self::assertStringContainsString('<span data-template', $markup);
        self::assertStringContainsString($collection->getTemplatePlaceholder(), $markup);
    }

    public function testDoesNotGenerateTemplateByDefault(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(false);

        $markup = $this->helper->render($collection);
        self::assertStringNotContainsString('<span data-template', $markup);
    }

    public function testCorrectlyIndexElementsInCollection(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');

        $markup = $this->helper->render($collection);
        self::assertStringContainsString('name="colors&#x5B;0&#x5D;"', $markup);
        self::assertStringContainsString('name="colors&#x5B;1&#x5D;"', $markup);
    }

    public function testCorrectlyIndexNestedElementsInCollection(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('fieldsets');

        $markup = $this->helper->render($collection);
        self::assertStringContainsString('fieldsets&#x5B;0&#x5D;&#x5B;field&#x5D;', $markup);
        self::assertStringContainsString('fieldsets&#x5B;1&#x5D;&#x5B;field&#x5D;', $markup);
        self::assertStringContainsString(
            'fieldsets&#x5B;1&#x5D;&#x5B;nested_fieldset&#x5D;&#x5B;anotherField&#x5D;',
            $markup
        );
    }

    public function testRenderWithCustomHelper(): void
    {
        $form = $this->getForm();

        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(false);

        $elementHelper = new CustomViewHelper();
        $elementHelper->setView($this->renderer);

        $markup = $this->helper->setElementHelper($elementHelper)->render($collection);

        self::assertStringContainsString('id="customcolors0"', $markup);
        self::assertStringContainsString('id="customcolors1"', $markup);
    }

    public function testRenderWithCustomFieldsetHelper(): void
    {
        $form = $this->getForm();

        $fieldsetHelper = new CustomFieldsetHelper();
        $fieldsetHelper->setView($this->renderer);

        $markup = $this->helper->setFieldsetHelper($fieldsetHelper)->render($form);

        self::assertStringContainsString('id="customFieldsetcolors"', $markup);
        self::assertStringContainsString('id="customFieldsetfieldsets"', $markup);
    }

    /**
     * @group issue-7167
     */
    public function testShouldNotWrapAtSubInvokeHelper(): void
    {
        self::assertStringNotContainsString(
            '<fieldset',
            $this->helper->__invoke($this->getForm(), false)
        );
    }

    /**
     * @group issue-7167
     */
    public function testShouldWrapAtRecursiveHelperCall(): void
    {
        self::assertStringContainsString(
            '<fieldset',
            $this->helper->__invoke($this->getForm())
        );
    }

    public function testShouldWrapReturnsDefaultTrue(): void
    {
        self::assertTrue($this->helper->shouldWrap());
    }

    public function testSetShouldWrapReturnsFalse(): void
    {
        $this->helper->setShouldWrap(false);
        self::assertFalse($this->helper->shouldWrap());
    }

    public function testGetDefaultElementHelperReturnsFormrow(): void
    {
        $defaultElement = $this->helper->getDefaultElementHelper();
        self::assertSame('formrow', $defaultElement);
    }

    public function testSetDefaultElementHelperToFoo(): void
    {
        $this->helper->setDefaultElementHelper('foo');
        $defaultElement = $this->helper->getDefaultElementHelper();
        self::assertSame('foo', $defaultElement);
    }

    public function testCanRenderTemplateAlone(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->renderTemplate($collection);
        self::assertStringContainsString('<span data-template', $markup);
        self::assertStringContainsString($collection->getTemplatePlaceholder(), $markup);
    }

    public function testCanTranslateLegend(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('untranslated legend');
        $this->helper->setShouldWrap(true);

        $mockTranslator = $this->createMock(Translator::class);
        $mockTranslator->expects($this->once())
                       ->method('translate')
                       ->willReturn('translated legend');

        $this->helper->setTranslator($mockTranslator);
        self::assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->render($collection);

        self::assertStringContainsString('>translated legend<', $markup);
    }

    public function testShouldWrapWithoutLabel(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);
        self::assertStringContainsString('<fieldset>', $markup);
    }

    public function testRenderCollectionAttributes(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('label');
        $this->helper->setShouldWrap(true);
        $collection->setAttribute('id', 'some-identifier');

        $markup = $this->helper->render($collection);
        self::assertStringContainsString(' id="some-identifier"', $markup);
    }

    public function testCanRenderFieldsetWithoutAttributes(): void
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        self::assertStringContainsString('<fieldset>', $html);
    }

    public function testCanRenderFieldsetWithAttributes(): void
    {
        $form = $this->getForm();
        $form->setAttributes([
            'id'    => 'foo-id',
            'class' => 'foo',
        ]);
        $html = $this->helper->render($form);
        self::assertMatchesRegularExpression('#<fieldset( [a-zA-Z]+\="[^"]+")+>#', $html);
        self::assertStringContainsString('id="foo-id"', $html);
        self::assertStringContainsString('class="foo"', $html);
    }

    public function testCanRenderWithoutLegend(): void
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        self::assertStringNotContainsString('<legend', $html);
        self::assertStringNotContainsString('</legend>', $html);
    }

    public function testRendersLabelAsLegend(): void
    {
        $form = $this->getForm();
        $form->setLabel('Foo');
        $html = $this->helper->render($form);
        self::assertMatchesRegularExpression('#<legend[^>]*>Foo#', $html);
        self::assertStringContainsString('</legend>', $html);
    }

    public function testCollectionIsWrappedByFieldsetWithoutLegend(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<legend>', $markup);
        self::assertStringStartsWith('<fieldset>', $markup);
        self::assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByFieldsetWithLabel(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        self::assertStringContainsString('<legend>foo</legend>', $markup);
        self::assertStringStartsWith('<fieldset>', $markup);
        self::assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByCustomElement(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<legend>', $markup);
        self::assertStringStartsWith('<div>', $markup);
        self::assertStringEndsWith('</div>', $markup);
    }

    public function testCollectionContainsTemplateAtPos3(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%3$s%2$s%1$s</div>');

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<legend>', $markup);
        self::assertStringStartsWith('<div><span', $markup);
        self::assertStringEndsWith('</div>', $markup);
    }

    public function testCollectionRendersLabelCorrectly(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);
        $this->helper->setLabelWrapper('<h1>%s</h1>');

        $markup = $this->helper->render($collection);

        self::assertStringContainsString('<h1>foo</h1>', $markup);
        self::assertStringStartsWith('<fieldset><h1>foo</h1>', $markup);
    }

    public function testCollectionCollectionRendersTemplateCorrectly(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<legend>', $markup);
        self::assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testCollectionRendersTemplateWithoutWrapper(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(false);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<fieldset>', $markup);
        self::assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testCollectionRendersFieldsetCorrectly(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        self::assertStringNotContainsString('<fieldset>', $markup);
        self::assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testGetterAndSetter(): void
    {
        self::assertSame($this->helper, $this->helper->setWrapper('foo'));
        self::assertSame('foo', $this->helper->getWrapper());
        self::assertEquals('foo', $this->helper->getWrapper());
        self::assertSame($this->helper, $this->helper->setLabelWrapper('foo'));
        self::assertSame('foo', $this->helper->getLabelWrapper());
        self::assertEquals('foo', $this->helper->getLabelWrapper());
        self::assertSame($this->helper, $this->helper->setTemplateWrapper('foo'));
        self::assertSame('foo', $this->helper->getTemplateWrapper());
        self::assertEquals('foo', $this->helper->getTemplateWrapper());
    }

    public function testLabelIsEscapedByDefault(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('<strong>Some label</strong>');
        $markup = $this->helper->render($collection);
        self::assertMatchesRegularExpression(
            '#<fieldset(.*?)><legend>&lt;strong&gt;Some label&lt;/strong&gt;<\/legend>(.*?)<\/fieldset>#',
            $markup
        );
    }

    public function testCanDisableLabelHtmlEscape(): void
    {
        $form       = $this->getForm();
        $collection = $form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setLabel('<strong>Some label</strong>');
        $collection->setLabelOptions(['disable_html_escape' => true]);
        $markup = $this->helper->render($collection);
        self::assertMatchesRegularExpression(
            '#<fieldset(.*?)><legend><strong>Some label</strong><\/legend>(.*?)<\/fieldset>#',
            $markup
        );
    }

    public function testForElementHelperNotInstanceOfHelperInterface(): void
    {
        $method = new ReflectionMethod(FormCollectionHelper::class, 'getElementHelper');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid element helper set in FormCollection.'
            . ' The helper must be an instance of Laminas\View\Helper\HelperInterface.'
        );

        $method->invokeArgs(new FormCollectionHelper(), []);
    }

    /**
     * @dataProvider provideDoctypesAndPermitFlagForNameAttribute
     */
    public function testRenderCollectionWithNameAttributeAndDoctypeHtml5(
        string $doctype,
        bool $allowsNameAttribute
    ): void {
        $this->helper->setDoctype($doctype);

        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setAttribute('name', 'foo');

        $markup = $this->helper->render($collection);
        if ($allowsNameAttribute) {
            self::assertStringContainsString('<fieldset name="foo">', $markup);
        } else {
            self::assertStringContainsString('<fieldset>', $markup);
        }
    }

    public static function provideDoctypesAndPermitFlagForNameAttribute(): array
    {
        return [
            [Doctype::XHTML11,             false],
            [Doctype::XHTML1_STRICT,       false],
            [Doctype::XHTML1_TRANSITIONAL, false],
            [Doctype::XHTML1_FRAMESET,     false],
            [Doctype::XHTML1_RDFA,         false],
            [Doctype::XHTML1_RDFA11,       false],
            [Doctype::XHTML_BASIC1,        false],
            [Doctype::XHTML5,              true],
            [Doctype::HTML4_STRICT,        false],
            [Doctype::HTML4_LOOSE,         false],
            [Doctype::HTML4_FRAMESET,      false],
            [Doctype::HTML5,               true],
        ];
    }

    /**
     * @dataProvider provideDoctypesAndPermitFlagForDisabledAttribute
     */
    public function testRenderCollectionWithDisabledAttribute(
        string $doctype,
        bool $allowsNameAttribute,
        bool $allowsShortAttribute
    ): void {
        $this->helper->setDoctype($doctype);

        $form       = $this->getForm();
        $collection = $form->get('colors');
        $collection->setAttribute('disabled', true);

        $markup = $this->helper->render($collection);

        if ($allowsNameAttribute) {
            if ($allowsShortAttribute) {
                self::assertStringContainsString('<fieldset name="colors" disabled>', $markup);
            } else {
                self::assertStringContainsString('<fieldset name="colors" disabled="disabled">', $markup);
            }
        } else {
            self::assertStringContainsString('<fieldset>', $markup);
        }
    }

    public static function provideDoctypesAndPermitFlagForDisabledAttribute(): array
    {
        return [
            [
                'doctype'              => Doctype::XHTML11,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML1_STRICT,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML1_TRANSITIONAL,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML1_FRAMESET,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML1_RDFA,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML1_RDFA11,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML_BASIC1,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::XHTML5,
                'allowsNameAttribute'  => true,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::HTML4_STRICT,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::HTML4_LOOSE,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::HTML4_FRAMESET,
                'allowsNameAttribute'  => false,
                'allowsShortAttribute' => false,
            ],
            [
                'doctype'              => Doctype::HTML5,
                'allowsNameAttribute'  => true,
                'allowsShortAttribute' => true,
            ],
        ];
    }
}
