<?php

declare(strict_types=1);

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
final class FormDateTimeSelectTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper = new FormDateTimeSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new DateTimeSelect();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesFiveSelectsWithElementByDefault(): void
    {
        $element = new DateTimeSelect('foo');
        $markup  = $this->helper->render($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<select name="hour"', $markup);
        self::assertStringContainsString('<select name="minute"', $markup);
        self::assertStringNotContainsString('<select name="second"', $markup);
    }

    public function testGeneratesSecondSelectIfAskedByElement(): void
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldShowSeconds(true);
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<select name="hour"', $markup);
        self::assertStringContainsString('<select name="minute"', $markup);
        self::assertStringContainsString('<select name="second"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption(): void
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<select name="hour"', $markup);
        self::assertStringContainsString('<select name="minute"', $markup);
        self::assertStringContainsString('<option value=""></option>', $markup);
    }

    public function testCanDisableDelimiters(): void
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(false);
        $markup = $this->helper->render($element);

        // If it contains two consecutive selects this means that no delimiters
        // are inserted
        self::assertStringContainsString('</select><select', $markup);
    }

    public function testCanRenderTextDelimiters(): void
    {
        $element = new DateTimeSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $element->setShouldRenderDelimiters(true);
        $element->setShouldShowSeconds(true);
        $markup = $this->helper->__invoke($element, IntlDateFormatter::LONG, IntlDateFormatter::LONG, 'pt_BR');

        // pattern === "d 'de' MMMM 'de' y HH'h'mm'min'ss's'"
        self::assertStringMatchesFormat('%a de %a de %a %ah%amin%as%a', $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new DateTimeSelect('foo');
        $markup  = $this->helper->__invoke($element);
        self::assertStringContainsString('<select name="day"', $markup);
        self::assertStringContainsString('<select name="month"', $markup);
        self::assertStringContainsString('<select name="year"', $markup);
        self::assertStringContainsString('<select name="hour"', $markup);
        self::assertStringContainsString('<select name="minute"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testNoMinutesDelimiterIfSecondsNotShown(): void
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
        $element->setShouldRenderDelimiters(true);
        $markup = $this->helper->__invoke($element);

        // the last $markup char should be the '>' of the minutes  html select
        // closing tag and not the delimiter
        self::assertEquals('>', substr($markup, -1));
    }

    /**
     * The `es_CL` locale has a short date pattern of `dd-MM-yy`, which should be supported
     * by our internal pattern parsing logic.
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

        $element = new DateTimeSelect('foo');
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
    </select>, <select name="hour">
        <option value="00">00</option>
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
    </select>:<select name="minute">
        <option value="00">00</option>
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
        <option value="32">32</option>
        <option value="33">33</option>
        <option value="34">34</option>
        <option value="35">35</option>
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
        <option value="42">42</option>
        <option value="43">43</option>
        <option value="44">44</option>
        <option value="45">45</option>
        <option value="46">46</option>
        <option value="47">47</option>
        <option value="48">48</option>
        <option value="49">49</option>
        <option value="50">50</option>
        <option value="51">51</option>
        <option value="52">52</option>
        <option value="53">53</option>
        <option value="54">54</option>
        <option value="55">55</option>
        <option value="56">56</option>
        <option value="57">57</option>
        <option value="58">58</option>
        <option value="59">59</option>
    </select>
</html>
XML,
            '<html>' . $this->helper->render($element) . '</html>'
        );
    }
}
