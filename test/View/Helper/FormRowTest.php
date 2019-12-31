<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormRow as FormRowHelper;
use Laminas\Form\View\HelperConfig;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTest
 */
class FormRowTest extends TestCase
{
    protected $helper;
    protected $renderer;

    public function setUp()
    {
        $this->helper = new FormRowHelper();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function testCanGenerateLabel()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->render($element);
        $this->assertContains('>The value for foo:<', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueBeforeInput()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $this->helper->setLabelPosition('prepend');
        $markup = $this->helper->render($element);
        $this->assertContains('<label><span>The value for foo:</span><', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueAfterInput()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'label' => 'The value for foo:',
        ));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains('<label><input', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanRenderRowLabelAttributes()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $element->setLabelAttributes(array('class' => 'bar'));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains("<label class=\"bar\">", $markup);
    }

    public function testCanCreateMarkupWithoutLabel()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'text');
        $markup = $this->helper->render($element);
        $this->assertRegexp('/<input name="foo" type="text"[^\/>]*\/?>/', $markup);
    }

    public function testCanHandleMultiCheckboxesCorrectly()
    {
        $options = array(
            'This is the first label' => 'value1',
            'This is the second label' => 'value2',
            'This is the third label' => 'value3',
        );

        $element = new Element\MultiCheckbox('foo');
        $element->setAttribute('type', 'multi_checkbox');
        $element->setAttribute('options', $options);
        $element->setLabel('This is a multi-checkbox');
        $markup = $this->helper->render($element);
        $this->assertContains("<fieldset>", $markup);
        $this->assertContains("<legend>", $markup);
        $this->assertContains("<label>", $markup);
    }

    public function testCanRenderErrors()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'First error message',
            'Second error message',
            'Third error message',
        ));

        $markup = $this->helper->render($element);
        $this->assertRegexp('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
    }

    public function testDoesNotRenderErrorsListIfSetToFalse()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'First error message',
            'Second error message',
            'Third error message',
        ));

        $markup = $this->helper->setRenderErrors(false)->render($element);
        $this->assertRegexp('/<input name="foo" class="input-error" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testCanModifyDefaultErrorClass()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'Error message'
        ));

        $markup = $this->helper->setInputErrorClass('custom-error-class')->render($element);
        $this->assertRegexp('/<input name="foo" class="custom-error-class" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testDoesNotOverrideClassesIfAlreadyPresentWhenThereAreErrors()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'Error message'
        ));
        $element->setAttribute('class', 'foo bar');

        $markup = $this->helper->setInputErrorClass('custom-error-class')->render($element);
        $this->assertRegexp('/<input name="foo" class="foo bar custom-error-class" type="text" [^\/>]*\/?>/', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
