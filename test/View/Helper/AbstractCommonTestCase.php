<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\ConfigProvider;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\View\Helper\Doctype;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;

use function extension_loaded;

/**
 * Abstract base test case for all form view helpers
 */
abstract class AbstractCommonTestCase extends TestCase
{
    protected AbstractHelper $helper;
    protected PhpRenderer $renderer;

    protected function setUp(): void
    {
        Doctype::unsetDoctypeRegistry();

        $this->renderer      = new PhpRenderer();
        $helperPluginManager = $this->renderer->getHelperPluginManager();
        $viewHelperConfig    = (new ConfigProvider())->getViewHelperConfig();
        $helperPluginManager->configure($viewHelperConfig);
        $this->renderer->setHelperPluginManager($helperPluginManager);

        $this->helper->setView($this->renderer);
    }

    public function testUsesUtf8ByDefault(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        self::assertEquals('UTF-8', $this->helper->getEncoding());
    }

    public function testCanInjectEncoding(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper->setEncoding('iso-8859-1');
        self::assertEquals('iso-8859-1', $this->helper->getEncoding());
    }

    public function testInjectingEncodingProxiesToEscapeHelper(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $escape = $this->renderer->plugin('escapehtml');
        self::assertInstanceOf(EscapeHtml::class, $escape);
        $this->helper->setEncoding('iso-8859-1');
        self::assertEquals('iso-8859-1', $escape->getEncoding());
    }

    public function testInjectingEncodingProxiesToAttrEscapeHelper(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $escape = $this->renderer->plugin('escapehtmlattr');
        self::assertInstanceOf(EscapeHtmlAttr::class, $escape);
        $this->helper->setEncoding('iso-8859-1');
        self::assertEquals('iso-8859-1', $escape->getEncoding());
    }

    public function testAssumesHtml4LooseDoctypeByDefault(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $helperClass = $this->helper::class;
        $helper      = new $helperClass();
        self::assertEquals(Doctype::HTML4_LOOSE, $helper->getDoctype());
    }

    public function testCanInjectDoctype(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper->setDoctype(Doctype::HTML5);
        self::assertEquals(Doctype::HTML5, $this->helper->getDoctype());
    }

    public function testCanGetDoctypeFromDoctypeHelper(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->renderer->doctype(Doctype::XHTML1_STRICT);
        self::assertEquals(Doctype::XHTML1_STRICT, $this->helper->getDoctype());
    }
}
