<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\View\Helper\File\FormFileSessionProgress;

class FormFileSessionProgressTest extends CommonTestCase
{
    public function setUp()
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
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="hidden"', $markup);
        $this->assertContains('id="progress_key"', $markup);
        $this->assertContains('name="' . $name . '"', $markup);
        $this->assertContains('value="', $markup);
    }
}
