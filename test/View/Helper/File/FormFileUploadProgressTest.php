<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\View\Helper\File\FormFileUploadProgress;

class FormFileUploadProgressTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormFileUploadProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes()
    {
        $markup  = $this->helper->__invoke();
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="hidden"', $markup);
        $this->assertContains('id="progress_key"', $markup);
        $this->assertContains('name="UPLOAD_IDENTIFIER"', $markup);
        $this->assertContains('value="', $markup);
    }
}
