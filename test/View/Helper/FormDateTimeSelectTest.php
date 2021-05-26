<?php

namespace LaminasTest\Form\View\Helper;

use IntlDateFormatter;
use Laminas\Form\Element\DateTimeSelect;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormDateTimeSelect as FormDateTimeSelectHelper;

use function extension_loaded;
use function substr;

/**
 * @property FormDateTimeSelectHelper $helper
 */
class FormDateTimeSelectTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new FormDateTimeSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new DateTimeSelect();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesFiveSelectsWithElementByDefault()
    {
        $element = new DateTimeSelect('foo');
        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<select name="hour"', $markup);
        $this->assertStringContainsString('<select name="minute"', $markup);
        $this->assertStringNotContainsString('<select name="second"', $markup);
    }

    public function testGeneratesSecondSelectIfAskedByElement()
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldShowSeconds(true);
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<select name="hour"', $markup);
        $this->assertStringContainsString('<select name="minute"', $markup);
        $this->assertStringContainsString('<select name="second"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption()
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<select name="hour"', $markup);
        $this->assertStringContainsString('<select name="minute"', $markup);
        $this->assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters()
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        $this->assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters()
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $element->setShouldShowSeconds(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "d 'de' MMMM 'de' y HH'h'mm'min'ss's'"
        $this->assertStringMatchesFormat('%a de %a de %a %ah%amin%as%a', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new DateTimeSelect('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertStringContainsString('<select name="day"', $markup);
        $this->assertStringContainsString('<select name="month"', $markup);
        $this->assertStringContainsString('<select name="year"', $markup);
        $this->assertStringContainsString('<select name="hour"', $markup);
        $this->assertStringContainsString('<select name="minute"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testNoMinutesDelimiterIfSecondsNotShown()
    {
        $element = new DateTimeSelect('foo');
        $element->setValue([
            'year'   => '2012',
            'month'  => '09',
            'day'    => '24',
            'hour'   => '03',
            'minute' => '04',
            'second' => '59',
        ]);

        $element->setShouldShowSeconds(false);
        $element->shouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element);

        // the last $markup char should be the '>' of the minutes  html select
        // closing tag and not the delimiter
        $this->assertEquals('>', substr($markup, -1));
    }
}
