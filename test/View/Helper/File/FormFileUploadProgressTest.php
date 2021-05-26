<?php

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileUploadProgress;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

class FormFileUploadProgressTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormFileUploadProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes()
    {
        $markup = $this->helper->__invoke();
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('id="progress_key"', $markup);
        $this->assertStringContainsString('name="UPLOAD_IDENTIFIER"', $markup);
        $this->assertStringContainsString('value="', $markup);
    }
}
