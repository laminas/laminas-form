<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\Form\View\Helper\Form as FormHelper;
use Laminas\Stdlib\PriorityList;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtmlAttr;
use LaminasTest\Form\TestAsset\CityFieldset;

use function sprintf;

/**
 * @property FormHelper $helper
 */
final class FormTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormHelper();
        parent::setUp();
    }

    public function testInvokeReturnsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCallingOpenTagWithoutProvidingFormResultsInEmptyActionAndGetMethod(): void
    {
        $markup = $this->helper->openTag();
        self::assertStringContainsString('<form', $markup);
        self::assertStringContainsString('action=""', $markup);
        self::assertStringContainsString('method="get"', $markup);
    }

    public function testCallingCloseTagEmitsClosingFormTag(): void
    {
        $markup = $this->helper->closeTag();
        self::assertEquals('</form>', $markup);
    }

    public function testCallingOpenTagWithFormUsesFormAttributes(): void
    {
        $form       = new Form();
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
        self::assertInstanceOf(EscapeHtmlAttr::class, $escape);
        foreach ($attributes as $attribute => $value) {
            self::assertStringContainsString(sprintf('%s="%s"', $attribute, $escape($value)), $markup);
        }
    }

    public function testOpenTagUsesNameAsIdIfNoIdAttributePresent(): void
    {
        $form       = new Form();
        $attributes = [
            'name' => 'login-form',
        ];
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);
        self::assertStringContainsString('name="login-form"', $markup);
        self::assertStringContainsString('id="login-form"', $markup);
    }

    public function testRender(): void
    {
        $form       = new Form();
        $attributes = ['name' => 'login-form'];
        $form->setAttributes($attributes);
        $form->add(new CityFieldset());
        $form->add(new Submit('send'));

        $markup = $this->helper->__invoke($form);

        self::assertStringContainsString('<form', $markup);
        self::assertStringContainsString('id="login-form"', $markup);
        self::assertStringContainsString('<label><span>Name of the city</span>', $markup);
        self::assertStringContainsString('<fieldset><legend>Country</legend>', $markup);
        self::assertStringContainsString('<input type="submit" name="send"', $markup);
        self::assertStringContainsString('</form>', $markup);
    }

    public function testRenderPreparesForm(): void
    {
        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('prepare');
        $form->method('getAttributes')->willReturn([]);
        $form->method('getIterator')->willReturn(new PriorityList());

        $markup = $this->helper->__invoke($form);

        self::assertStringContainsString('<form', $markup);
        self::assertStringContainsString('</form>', $markup);
    }

    public function testHtml5DoesNotAddEmptyActionAttributeToFormTag(): void
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        self::assertStringContainsString('action=""', $html4Markup);
        self::assertStringNotContainsString('action=""', $html5Markup);
        self::assertStringNotContainsString('action=""', $xhtml5Markup);
    }

    public function testHtml5DoesNotSetDefaultMethodAttributeInFormTag(): void
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        self::assertStringContainsString('method="get"', $html4Markup);
        self::assertStringNotContainsString('method="get"', $html5Markup);
        self::assertStringNotContainsString('method="get"', $xhtml5Markup);
    }
}
