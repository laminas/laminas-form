<?php

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileSessionProgress;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

use function ini_get;

/**
 * @property FormFileSessionProgress $helper
 */
class FormFileSessionProgressTest extends AbstractCommonTestCase
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
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('id="progress_key"', $markup);
        $this->assertStringContainsString('name="' . $name . '"', $markup);
        $this->assertStringContainsString('value="', $markup);
    }
}
