<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormElementErrors as FormElementErrorsHelper;

class FormElementErrorsTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormElementErrorsHelper();
        parent::setUp();
    }

    public function getMessageList()
    {
        return [
            'First error message',
            'Second error message',
            'Third error message',
        ];
    }

    public function testLackOfMessagesResultsInEmptyMarkup()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertEquals('', $markup);
    }

    public function testRendersErrorMessagesUsingUnorderedListByDefault()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element);
        $this->assertRegexp('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
    }

    public function testCanSpecifyAttributesForOpeningTag()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertContains('ul class="error"', $markup);
    }

    public function testCanSpecifyAttributesForOpeningTagUsingInvoke()
    {
        $helper = $this->helper;
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $helper($element, ['class' => 'error']);
        $this->assertContains('ul class="error"', $markup);
    }

    public function testCanSpecifyAlternateMarkupStringsViaSetters()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $this->helper->setMessageOpenFormat('<div%s><span>')
                     ->setMessageCloseString('</span></div>')
                     ->setMessageSeparatorString('</span><span>')
                     ->setAttributes(['class' => 'error']);

        $markup = $this->helper->render($element);
        $this->assertRegexp('#<div class="error">\s*<span>First error message</span>\s*<span>Second error message</span>\s*<span>Third error message</span>\s*</div>#s', $markup);
    }

    public function testSpecifiedAttributesOverrideDefaults()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);
        $element->setAttributes(['class' => 'foo']);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertContains('ul class="error"', $markup);
    }

    public function testGetAttributes()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $this->helper->setAttributes(['class' => 'error']);

        $this->helper->render($element);

        $this->assertEquals(['class' => 'error'], $this->helper->getAttributes());
    }

    public function testRendersNestedMessageSetsAsAFlatList()
    {
        $messages = [
            [
                'First validator message',
            ],
            [
                'Second validator first message',
                'Second validator second message',
            ],
        ];
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertRegexp('#<ul class="error">\s*<li>First validator message</li>\s*<li>Second validator first message</li>\s*<li>Second validator second message</li>\s*</ul>#s', $markup);
    }

    public function testCallingTheHelperToRenderInvokeCanReturnObject()
    {
        $helper = $this->helper;
        $this->assertEquals($helper(), $helper);
    }
}
