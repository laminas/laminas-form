<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use IntlDateFormatter;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Select;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormDateSelect as FormDateSelectHelper;

use function extension_loaded;

/**
 * @property FormDateSelectHelper $helper
 */
final class FormDateSelectTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new FormDateSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new DateSelect();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesThreeSelectsWithElement(): void
    {
        $element = new DateSelect('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        $this->assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "d 'de' MMMM 'de' y"
        $this->assertStringMatchesFormat('%a de %a de %a', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new DateSelect('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testDayElementValueOptions(): void
    {
        $element = new DateSelect('foo');
        $this->helper->render($element);
        $this->assertCount(31, $element->getDayElement()->getValueOptions());
    }

    /**
     * @group issue-6656
     */
    public function testGetElements(): void
    {
        $element = new DateSelect('foo');
        $this->helper->render($element);
        $elements = $element->getElements();
        $this->assertCount(3, $elements);

        foreach ($elements as $subElement) {
            $this->assertInstanceOf(Select::class, $subElement);
        }

        $this->assertCount(31, $elements[0]->getValueOptions());
    }
}
