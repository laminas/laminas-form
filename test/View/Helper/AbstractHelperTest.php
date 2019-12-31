<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;
use Laminas\Escaper\Escaper;

/**
 * Tests for {@see \Laminas\Form\View\Helper\AbstractHelper}
 *
 * @covers \Laminas\Form\View\Helper\AbstractHelper
 */
class AbstractHelperTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = $this->getMockForAbstractClass('Laminas\Form\View\Helper\AbstractHelper');

        parent::setUp();
    }

    /**
     * @group 5991
     */
    public function testWillEscapeValueAttributeValuesCorrectly()
    {
        $this->assertSame(
            'data-value="breaking&#x20;your&#x20;HTML&#x20;like&#x20;a&#x20;boss&#x21;&#x20;&#x5C;"',
            $this->helper->createAttributesString(array('data-value' => 'breaking your HTML like a boss! \\'))
        );
    }

    public function testWillEncodeValueAttributeValuesCorrectly()
    {
        $escaper = new Escaper('iso-8859-1');

        $this->helper->setEncoding('iso-8859-1');

        $this->assertSame(
            'data-value="' . $escaper->escapeHtmlAttr('Título') . '"',
            $this->helper->createAttributesString(array('data-value' => 'Título'))
        );
    }

    public function testWillNotEncodeValueAttributeValuesCorrectly()
    {
        $escaper = new Escaper('iso-8859-1');

        $this->assertNotSame(
            'data-value="' . $escaper->escapeHtmlAttr('Título') . '"',
            $this->helper->createAttributesString(array('data-value' => 'Título'))
        );
    }
}
