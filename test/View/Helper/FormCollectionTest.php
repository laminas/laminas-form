<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\View\Helper\FormCollection as FormCollectionHelper;
use Laminas\Form\View\HelperConfig;
use Laminas\View\Helper\Doctype;
use Laminas\View\Renderer\PhpRenderer;
use LaminasTest\Form\TestAsset\CustomFieldsetHelper;
use LaminasTest\Form\TestAsset\CustomViewHelper;
use LaminasTest\Form\TestAsset\FormCollection;
use PHPUnit_Framework_TestCase as TestCase;

class FormCollectionTest extends TestCase
{
    public $helper;
    public $form;
    public $renderer;

    public function setUp()
    {
        $this->helper = new FormCollectionHelper();

        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
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
        $this->assertContains('<span data-template', $markup);
        $this->assertContains($collection->getTemplatePlaceholder(), $markup);
    }

    public function testDoesNotGenerateTemplateByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $markup = $this->helper->render($collection);
        $this->assertNotContains('<span data-template', $markup);
    }

    public function testCorrectlyIndexElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');

        $markup = $this->helper->render($collection);
        $this->assertContains('name="colors&#x5B;0&#x5D;"', $markup);
        $this->assertContains('name="colors&#x5B;1&#x5D;"', $markup);
    }

    public function testCorrectlyIndexNestedElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');

        $markup = $this->helper->render($collection);
        $this->assertContains('fieldsets&#x5B;0&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertContains('fieldsets&#x5B;1&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertContains('fieldsets&#x5B;1&#x5D;&#x5B;nested_fieldset&#x5D;&#x5B;anotherField&#x5D;', $markup);
    }

    public function testRenderWithCustomHelper()
    {
        $form = $this->getForm();

        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $elementHelper = new CustomViewHelper();
        $elementHelper->setView($this->renderer);

        $markup = $this->helper->setElementHelper($elementHelper)->render($collection);

        $this->assertContains('id="customcolors0"', $markup);
        $this->assertContains('id="customcolors1"', $markup);
    }

    public function testRenderWithCustomFieldsetHelper()
    {
        $form = $this->getForm();

        $fieldsetHelper = new CustomFieldsetHelper();
        $fieldsetHelper->setView($this->renderer);

        $markup = $this->helper->setFieldsetHelper($fieldsetHelper)->render($form);

        $this->assertContains('id="customFieldsetcolors"', $markup);
        $this->assertContains('id="customFieldsetfieldsets"', $markup);
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
        $this->assertContains('<span data-template', $markup);
        $this->assertContains($collection->getTemplatePlaceholder(), $markup);
    }

    public function testCanTranslateLegend()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('untranslated legend');
        $this->helper->setShouldWrap(true);

        $mockTranslator = $this->getMock('Laminas\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(1))
                       ->method('translate')
                       ->will($this->returnValue('translated legend'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->render($collection);

        $this->assertContains('>translated legend<', $markup);
    }

    public function testCanRenderFieldsetWithoutAttributes()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertContains('<fieldset>', $html);
    }

    public function testCanRenderFieldsetWithAttributes()
    {
        $form = $this->getForm();
        $form->setAttributes(array(
            'id'    => 'foo-id',
            'class' => 'foo',
        ));
        $html = $this->helper->render($form);
        $this->assertRegexp('#<fieldset( [a-zA-Z]+\="[^"]+")+>#', $html);
        $this->assertContains('id="foo-id"', $html);
        $this->assertContains('class="foo"', $html);
    }

    public function testCanRenderWithoutLegend()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertNotContains('<legend', $html);
        $this->assertNotContains('</legend>', $html);
    }

    public function testRendersLabelAsLegend()
    {
        $form = $this->getForm();
        $form->setLabel('Foo');
        $html = $this->helper->render($form);
        $this->assertRegExp('#<legend[^>]*>Foo#', $html);
        $this->assertContains('</legend>', $html);
    }
}
