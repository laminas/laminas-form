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
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
    }

    public function testGeneratesWithoutRenderer(): void
    {
        $element = new DateSelect('foo');
        $helper = new FormDateSelectHelper();
        $markup  = $helper->render($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        self::assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters(): void
    {
        $element = new DateSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "d 'de' MMMM 'de' y"
        self::assertStringMatchesFormat('%a de %a de %a', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new DateSelect('foo');
        $markup  = $this->helper->__invoke($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testDayElementValueOptions(): void
    {
        $element = new DateSelect('foo');
        $this->helper->render($element);
        self::assertCount(31, $element->getDayElement()->getValueOptions());
    }

    /**
     * @group issue-6656
     */
    public function testGetElements(): void
    {
        $element = new DateSelect('foo');
        $this->helper->render($element);
        $elements = $element->getElements();
        self::assertCount(3, $elements);

        foreach ($elements as $subElement) {
            self::assertInstanceOf(Select::class, $subElement);
        }

        self::assertCount(31, $elements[0]->getValueOptions());
    }

    /**
     * The `es_CL` locale has a short date pattern of `dd-MM-yy HH:mm:ss`, which should be
     * supported by our internal pattern parsing logic.
     *
     * This test verifies that such a pattern is correctly identified internally, and that
     * our date rendering matches the pattern too.
     *
     * This kind of test could be repeated for all defined locales, but `es_CL` is sufficient
     * for now, since this test was initially designed to catch a regression in the logic
     * that reads out `ext-intl` patterns for us.
     *
     * @group 160
     * @group 184
     */
    public function testRendersDatesWithEsCLLocaleDatePattern(): void
    {
        $this->helper->setLocale('es_CL');
        $this->helper->setDateType(IntlDateFormatter::SHORT);

        $element = new DateSelect('foo');
        $element->setMinYear(2022);
        $element->setMaxYear(2022);

        self::assertXmlStringEqualsXmlString(
            <<<'XML'
<html>
    <select name="day">
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
    </select>-<select name="month">
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
    </select>-<select name="year">
        <option value="2022">2022</option>
    </select>
</html>
XML,
            '<html>' . $this->helper->render($element) . '</html>'
        );
    }
}
