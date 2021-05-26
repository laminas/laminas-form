<?php

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileApcProgress;
use LaminasTest\Form\View\Helper\AbstractCommonTestCase;

use function ini_get;

/**
 * @property FormFileApcProgress $helper
 */
class FormFileApcProgressTest extends AbstractCommonTestCase
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
        $this->assertStringContainsString('<input ', $markup);
        $this->assertStringContainsString('type="hidden"', $markup);
        $this->assertStringContainsString('id="progress_key"', $markup);
        $this->assertStringContainsString('name="' . $name . '"', $markup);
        $this->assertStringContainsString('value="', $markup);
    }
}
