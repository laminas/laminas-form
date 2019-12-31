<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element\MonthSelect;
use Laminas\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTest
 */
class FormMonthSelectTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormMonthSelectHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new MonthSelect();
        $this->setExpectedException('Laminas\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesTwoSelectsWithElement()
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->render($element);
        $this->assertNotContains('<select name="day"', $markup);
        $this->assertContains('<select name="month"', $markup);
        $this->assertContains('<select name="year"', $markup);
    }

    public function testCanGenerateSelectsWithEmptyOption()
    {
        $element = new MonthSelect('foo');
        $element->setShouldCreateEmptyOption(true);
        $markup  = $this->helper->render($element);
        $this->assertNotContains('<select name="day"', $markup);
        $this->assertContains('<select name="month"', $markup);
        $this->assertContains('<select name="year"', $markup);
        $this->assertContains('<option value=""></option>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new MonthSelect('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertNotContains('<select name="day"', $markup);
        $this->assertContains('<select name="month"', $markup);
        $this->assertContains('<select name="year"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
