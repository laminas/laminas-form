<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileApcProgress;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

use function ini_get;

/**
 * @property FormFileApcProgress $helper
 */
final class FormFileApcProgressTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormFileApcProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes(): void
    {
        $name = ini_get('apc.rfc1867_name');
        if (false === $name) {
            $this->markTestSkipped('APC module is not active');
        }

        $markup = $this->helper->__invoke();
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="hidden"', $markup);
        self::assertStringContainsString('id="progress_key"', $markup);
        self::assertStringContainsString('name="' . $name . '"', $markup);
        self::assertStringContainsString('value="', $markup);
    }
}
