<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Exception\ExtensionNotLoadedException;
use Laminas\Form\View\Helper\FormDateSelect as FormDateSelectHelper;
use Laminas\Form\View\Helper\FormDateTimeSelect as FormDateTimeSelectHelper;
use Laminas\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;
use PHPUnit\Framework\TestCase;

use function extension_loaded;

final class MissingIntlExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl enabled');
        }
    }

    public function testFormDateSelectHelper(): void
    {
        $this->expectException(ExtensionNotLoadedException::class);
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormDateSelectHelper();
    }

    public function testFormDateTimeSelectHelper(): void
    {
        $this->expectException(ExtensionNotLoadedException::class);
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormDateTimeSelectHelper();
    }

    public function testFormMonthSelectHelper(): void
    {
        $this->expectException(ExtensionNotLoadedException::class);
        $this->expectExceptionMessage('Laminas\Form\View\Helper component requires the intl PHP extension');

        $helper = new FormMonthSelectHelper();
    }
}
