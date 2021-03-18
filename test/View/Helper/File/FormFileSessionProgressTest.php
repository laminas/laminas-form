<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper\File;

use Laminas\Form\View\Helper\File\FormFileSessionProgress;
use LaminasTest\Form\View\Helper\CommonTestCase;

use function ini_get;

class FormFileSessionProgressTest extends CommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormFileSessionProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes()
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
