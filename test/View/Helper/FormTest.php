<?php

namespace LaminasTest\Form\View\Helper;

use ArrayIterator;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\View\Helper\Form as FormHelper;
use Laminas\View\Helper\Doctype;
use LaminasTest\Form\TestAsset\CityFieldset;

use function sprintf;

class FormTest extends CommonTestCase
{
    protected function setUp(): void
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
        $this->assertStringContainsString('<form', $markup);
        $this->assertStringContainsString('action=""', $markup);
        $this->assertStringContainsString('method="get"', $markup);
    }

    public function testCallingCloseTagEmitsClosingFormTag()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</form>', $markup);
    }

    public function testCallingOpenTagWithFormUsesFormAttributes()
    {
        $form = new Form();
        $attributes = [
            'method'  => 'post',
            'action'  => 'http://localhost/endpoint',
            'class'   => 'login',
            'id'      => 'form-login',
            'enctype' => 'application/x-www-form-urlencoded',
            'target'  => '_self',
        ];
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);

        $escape = $this->renderer->plugin('escapehtmlattr');
        foreach ($attributes as $attribute => $value) {
            $this->assertStringContainsString(sprintf('%s="%s"', $attribute, $escape($value)), $markup);
        }
    }

    public function testOpenTagUsesNameAsIdIfNoIdAttributePresent()
    {
        $form = new Form();
        $attributes = [
            'name'  => 'login-form',
        ];
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);
        $this->assertStringContainsString('name="login-form"', $markup);
        $this->assertStringContainsString('id="login-form"', $markup);
    }

    public function testRender()
    {
        $form = new Form();
        $attributes = ['name'  => 'login-form'];
        $form->setAttributes($attributes);
        $form->add(new CityFieldset());
        $form->add(new Submit('send'));

        $markup = $this->helper->__invoke($form);

        $this->assertStringContainsString('<form', $markup);
        $this->assertStringContainsString('id="login-form"', $markup);
        $this->assertStringContainsString('<label><span>Name of the city</span>', $markup);
        $this->assertStringContainsString('<fieldset><legend>Country</legend>', $markup);
        $this->assertStringContainsString('<input type="submit" name="send"', $markup);
        $this->assertStringContainsString('</form>', $markup);
    }

    public function testRenderPreparesForm()
    {
        $form = $this->createMock('Laminas\\Form\\Form');
        $form->expects($this->once())->method('prepare');
        $form->method('getAttributes')->willReturn([]);
        $form->method('getIterator')->willReturn(new ArrayIterator([]));

        $markup = $this->helper->__invoke($form);

        $this->assertStringContainsString('<form', $markup);
        $this->assertStringContainsString('</form>', $markup);
    }

    public function testHtml5DoesNotAddEmptyActionAttributeToFormTag()
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        $this->assertStringContainsString('action=""', $html4Markup);
        $this->assertStringNotContainsString('action=""', $html5Markup);
        $this->assertStringNotContainsString('action=""', $xhtml5Markup);
    }

    public function testHtml5DoesNotSetDefaultMethodAttributeInFormTag()
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        $this->assertStringContainsString('method="get"', $html4Markup);
        $this->assertStringNotContainsString('method="get"', $html5Markup);
        $this->assertStringNotContainsString('method="get"', $xhtml5Markup);
    }
}
