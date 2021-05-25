<?php

namespace LaminasTest\Form\View\Helper;

use IntlDateFormatter;
use Laminas\Form\Element\MonthSelect;
use Laminas\Form\Element\Select;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

use function extension_loaded;

class FormMonthSelectTest extends CommonTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new FormMonthSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new MonthSelect();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesTwoSelectsWithElement()
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringNotContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption()
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        $this->assertStringNotContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters()
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        $this->assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters()
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "MMMM 'de' y"
        $this->assertStringMatchesFormat('%a de %a', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringNotContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testMonthElementValueOptions()
    {
        $element = new MonthSelect('foo');
        $this->helper->render($element);
        $this->assertCount(12, $element->getMonthElement()->getValueOptions());
    }

    /**
     * @group issue-6656
     */
    public function testGetElements()
    {
        $element = new MonthSelect('foo');
        $this->helper->render($element);
        $elements = $element->getElements();
        $this->assertCount(2, $elements);

        foreach ($elements as $subElement) {
            $this->assertInstanceOf(Select::class, $subElement);
        }

        $this->assertCount(12, $elements[0]->getValueOptions());
    }
}
