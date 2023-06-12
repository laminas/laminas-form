<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use IntlDateFormatter;
use Laminas\Form\Element\MonthSelect;
use Laminas\Form\Element\Select;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

use function extension_loaded;

/**
 * @property FormMonthSelectHelper $helper
 */
final class FormMonthSelectTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new FormMonthSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new MonthSelect();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesTwoSelectsWithElement(): void
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->render($element);
        self::assertStringNotContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption(): void
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        self::assertStringNotContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters(): void
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        self::assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters(): void
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "MMMM 'de' y"
        self::assertStringMatchesFormat('%a de %a', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->__invoke($element);
        self::assertStringNotContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testMonthElementValueOptions(): void
    {
        $element = new MonthSelect('foo');
        $this->helper->render($element);
        self::assertCount(12, $element->getMonthElement()->getValueOptions());
    }

    /**
     * @group issue-6656
     */
    public function testGetElements(): void
    {
        $element = new MonthSelect('foo');
        $this->helper->render($element);
        $elements = $element->getElements();
        self::assertCount(2, $elements);

        foreach ($elements as $subElement) {
            self::assertInstanceOf(Select::class, $subElement);
        }

        self::assertCount(12, $elements[0]->getValueOptions());
    }

    public function testRendersDatesWithArARLocaleContainsSelectOptionsWithOnlyNumericValues(): void
    {
        $this->helper->setLocale('ar_AR');
        $this->helper->setDateType(IntlDateFormatter::LONG);

        $element = new MonthSelect('foo');

        $element->setMinYear(2022);
        $element->setMaxYear(2023);

        self::assertDoesNotMatchRegularExpression(
            self::NON_NUMERIC_OPTION_REGEX,
            $this->helper->render($element)
        );
    }
}
