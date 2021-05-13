<?php

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\View\Helper\FormCollection as FormCollectionHelper;
use LaminasTest\Form\TestAsset\CustomFieldsetHelper;
use LaminasTest\Form\TestAsset\CustomViewHelper;
use LaminasTest\Form\TestAsset\FormCollection;
use ReflectionMethod;

class FormCollectionTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormCollectionHelper();
        parent::setUp();
    }

    public function getForm()
    {
        $form = new FormCollection();
        $form->prepare();

        return $form;
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCanGenerateTemplate()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->render($collection);
        $this->assertStringContainsString('<span data-template', $markup);
        $this->assertStringContainsString($collection->getTemplatePlaceholder(), $markup);
    }

    public function testDoesNotGenerateTemplateByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $markup = $this->helper->render($collection);
        $this->assertStringNotContainsString('<span data-template', $markup);
    }

    public function testCorrectlyIndexElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');

        $markup = $this->helper->render($collection);
        $this->assertStringContainsString('name="colors&#x5B;0&#x5D;"', $markup);
        $this->assertStringContainsString('name="colors&#x5B;1&#x5D;"', $markup);
    }

    public function testCorrectlyIndexNestedElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');

        $markup = $this->helper->render($collection);
        $this->assertStringContainsString('fieldsets&#x5B;0&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertStringContainsString('fieldsets&#x5B;1&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertStringContainsString(
            'fieldsets&#x5B;1&#x5D;&#x5B;nested_fieldset&#x5D;&#x5B;anotherField&#x5D;',
            $markup
        );
    }

    public function testRenderWithCustomHelper()
    {
        $form = $this->getForm();

        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $elementHelper = new CustomViewHelper();
        $elementHelper->setView($this->renderer);

        $markup = $this->helper->setElementHelper($elementHelper)->render($collection);

        $this->assertStringContainsString('id="customcolors0"', $markup);
        $this->assertStringContainsString('id="customcolors1"', $markup);
    }

    public function testRenderWithCustomFieldsetHelper()
    {
        $form = $this->getForm();

        $fieldsetHelper = new CustomFieldsetHelper();
        $fieldsetHelper->setView($this->renderer);

        $markup = $this->helper->setFieldsetHelper($fieldsetHelper)->render($form);

        $this->assertStringContainsString('id="customFieldsetcolors"', $markup);
        $this->assertStringContainsString('id="customFieldsetfieldsets"', $markup);
    }

    /**
     * @group issue-7167
     */
    public function testShouldNotWrapAtSubInvokeHelper()
    {
        $this->assertStringNotContainsString(
            '<fieldset',
            $this->helper->__invoke($this->getForm(), false)
        );
    }

    /**
     * @group issue-7167
     */
    public function testShouldWrapAtRecursiveHelperCall()
    {
        $this->assertStringContainsString(
            '<fieldset',
            $this->helper->__invoke($this->getForm())
        );
    }

    public function testShouldWrapReturnsDefaultTrue()
    {
        $this->assertTrue($this->helper->shouldWrap());
    }

    public function testSetShouldWrapReturnsFalse()
    {
        $this->helper->setShouldWrap(false);
        $this->assertFalse($this->helper->shouldWrap());
    }

    public function testGetDefaultElementHelperReturnsFormrow()
    {
        $defaultElement = $this->helper->getDefaultElementHelper();
        $this->assertSame('formrow', $defaultElement);
    }

    public function testSetDefaultElementHelperToFoo()
    {
        $this->helper->setDefaultElementHelper('foo');
        $defaultElement = $this->helper->getDefaultElementHelper();
        $this->assertSame('foo', $defaultElement);
    }

    public function testCanRenderTemplateAlone()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->renderTemplate($collection);
        $this->assertStringContainsString('<span data-template', $markup);
        $this->assertStringContainsString($collection->getTemplatePlaceholder(), $markup);
    }

    public function testCanTranslateLegend()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('untranslated legend');
        $this->helper->setShouldWrap(true);

        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');
        $mockTranslator->expects($this->once())
                       ->method('translate')
                       ->willReturn('translated legend');

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->render($collection);

        $this->assertStringContainsString('>translated legend<', $markup);
    }

    public function testShouldWrapWithoutLabel()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);
        $this->assertStringContainsString('<fieldset>', $markup);
    }

    public function testRenderCollectionAttributes()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('label');
        $this->helper->setShouldWrap(true);
        $collection->setAttribute('id', 'some-identifier');

        $markup = $this->helper->render($collection);
        $this->assertStringContainsString(' id="some-identifier"', $markup);
    }

    public function testCanRenderFieldsetWithoutAttributes()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertStringContainsString('<fieldset>', $html);
    }

    public function testCanRenderFieldsetWithAttributes()
    {
        $form = $this->getForm();
        $form->setAttributes([
            'id'    => 'foo-id',
            'class' => 'foo',
        ]);
        $html = $this->helper->render($form);
        $this->assertMatchesRegularExpression('#<fieldset( [a-zA-Z]+\="[^"]+")+>#', $html);
        $this->assertStringContainsString('id="foo-id"', $html);
        $this->assertStringContainsString('class="foo"', $html);
    }

    public function testCanRenderWithoutLegend()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertStringNotContainsString('<legend', $html);
        $this->assertStringNotContainsString('</legend>', $html);
    }

    public function testRendersLabelAsLegend()
    {
        $form = $this->getForm();
        $form->setLabel('Foo');
        $html = $this->helper->render($form);
        $this->assertMatchesRegularExpression('#<legend[^>]*>Foo#', $html);
        $this->assertStringContainsString('</legend>', $html);
    }

    public function testCollectionIsWrappedByFieldsetWithoutLegend()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<legend>', $markup);
        $this->assertStringStartsWith('<fieldset>', $markup);
        $this->assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByFieldsetWithLabel()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        $this->assertStringContainsString('<legend>foo</legend>', $markup);
        $this->assertStringStartsWith('<fieldset>', $markup);
        $this->assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByCustomElement()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<legend>', $markup);
        $this->assertStringStartsWith('<div>', $markup);
        $this->assertStringEndsWith('</div>', $markup);
    }

    public function testCollectionContainsTemplateAtPos3()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%3$s%2$s%1$s</div>');

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<legend>', $markup);
        $this->assertStringStartsWith('<div><span', $markup);
        $this->assertStringEndsWith('</div>', $markup);
    }

    public function testCollectionRendersLabelCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);
        $this->helper->setLabelWrapper('<h1>%s</h1>');

        $markup = $this->helper->render($collection);

        $this->assertStringContainsString('<h1>foo</h1>', $markup);
        $this->assertStringStartsWith('<fieldset><h1>foo</h1>', $markup);
    }

    public function testCollectionCollectionRendersTemplateCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<legend>', $markup);
        $this->assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testCollectionRendersTemplateWithoutWrapper()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(false);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<fieldset>', $markup);
        $this->assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testCollectionRendersFieldsetCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertStringNotContainsString('<fieldset>', $markup);
        $this->assertMatchesRegularExpression('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testGetterAndSetter()
    {
        $this->assertSame($this->helper, $this->helper->setWrapper('foo'));
        $this->assertSame('foo', $this->helper->getWrapper());
        $this->assertEquals('foo', $this->helper->getWrapper());
        $this->assertSame($this->helper, $this->helper->setLabelWrapper('foo'));
        $this->assertSame('foo', $this->helper->getLabelWrapper());
        $this->assertEquals('foo', $this->helper->getLabelWrapper());
        $this->assertSame($this->helper, $this->helper->setTemplateWrapper('foo'));
        $this->assertSame('foo', $this->helper->getTemplateWrapper());
        $this->assertEquals('foo', $this->helper->getTemplateWrapper());
    }

    public function testLabelIsEscapedByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('<strong>Some label</strong>');
        $markup = $this->helper->render($collection);
        $this->assertMatchesRegularExpression(
            '#<fieldset(.*?)><legend>&lt;strong&gt;Some label&lt;/strong&gt;<\/legend>(.*?)<\/fieldset>#',
            $markup
        );
    }

    public function testCanDisableLabelHtmlEscape()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('<strong>Some label</strong>');
        $collection->setLabelOptions(['disable_html_escape' => true]);
        $markup = $this->helper->render($collection);
        $this->assertMatchesRegularExpression(
            '#<fieldset(.*?)><legend><strong>Some label</strong><\/legend>(.*?)<\/fieldset>#',
            $markup
        );
    }

    public function testForElementHelperNotInstanceOfHelperInterface()
    {
        $method = new ReflectionMethod('Laminas\Form\View\Helper\FormCollection', 'getElementHelper');
        $method->setAccessible(true);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage(
            'Invalid element helper set in FormCollection.'
            . ' The helper must be an instance of Laminas\View\Helper\HelperInterface.'
        );


        $method->invokeArgs(new FormCollectionHelper(), []);
    }
}
