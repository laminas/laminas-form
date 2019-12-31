<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\View\Helper\FormDateSelect as FormDateSelectHelper;
use Laminas\Form\View\Helper\FormDateTimeSelect as FormDateTimeSelectHelper;
use Laminas\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;
use PHPUnit\Framework\TestCase;

class MissingIntlExtensionTest extends TestCase
{
    public function setUp()
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl enabled');
        }
    }

    public function testFormDateSelectHelper()
    {
        $this->expectException('Laminas\Form\Exception\ExtensionNotLoadedException');
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormDateSelectHelper();
    }

    public function testFormDateTimeSelectHelper()
    {
        $this->expectException('Laminas\Form\Exception\ExtensionNotLoadedException');
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormDateTimeSelectHelper();
    }

    public function testFormMonthSelectHelper()
    {
        $this->expectException('Laminas\Form\Exception\ExtensionNotLoadedException');
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormMonthSelectHelper();
    }
}
