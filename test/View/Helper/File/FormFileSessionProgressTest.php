<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileSessionProgress;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

use function ini_get;

/**
 * @property FormFileSessionProgress $helper
 */
final class FormFileSessionProgressTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormFileSessionProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes(): void
    {
        $name = ini_get('session.upload_progress.name');
        if (false === $name) {
            $this->markTestSkipped('Session Upload Progress feature is not active');
        }

        $markup = $this->helper->__invoke();
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="hidden"', $markup);
        self::assertStringContainsString('id="progress_key"', $markup);
        self::assertStringContainsString('name="' . $name . '"', $markup);
        self::assertStringContainsString('value="', $markup);
    }
}
