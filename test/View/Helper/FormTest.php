<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\Form\View\Helper\Form as FormHelper;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage View
 */
class FormTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormHelper();
        parent::setUp();
    }

    public function testInvokeReturnsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCallingOpenTagWithoutProvidingFormResultsInEmptyActionAndGetMethod()
    {
        $markup = $this->helper->openTag();
        $this->assertContains('<form', $markup);
        $this->assertContains('action=""', $markup);
        $this->assertContains('method="get"', $markup);
    }

    public function testCallingCloseTagEmitsClosingFormTag()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</form>', $markup);
    }

    public function testCallingOpenTagWithFormUsesFormAttributes()
    {
        $form = new Form();
        $attributes = array(
            'method'  => 'post',
            'action'  => 'http://localhost/endpoint',
            'class'   => 'login',
            'id'      => 'form-login',
            'enctype' => 'application/x-www-form-urlencoded',
            'target'  => '_self',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);

        $escape = $this->renderer->plugin('escapehtml');
        foreach ($attributes as $attribute => $value) {
            $this->assertContains(sprintf('%s="%s"', $attribute, $escape($value)), $markup);
        }
    }

    public function testOpenTagUsesNameAsIdIfNoIdAttributePresent()
    {
        $form = new Form();
        $attributes = array(
            'name'  => 'login-form',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);
        $this->assertContains('name="login-form"', $markup);
        $this->assertContains('id="login-form"', $markup);
    }
}
