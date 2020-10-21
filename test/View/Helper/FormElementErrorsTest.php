<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\View\Helper\FormElementErrors as FormElementErrorsHelper;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Translator\TranslatorInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class FormElementErrorsTest extends CommonTestCase
{
    use ProphecyTrait;

    /**
     * @var null|TranslatorInterface
     */
    protected $defaultTranslator;

    protected function setUp(): void
    {
        $this->defaultTranslator = AbstractValidator::getDefaultTranslator();
        $this->helper = new FormElementErrorsHelper();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        AbstractValidator::setDefaultTranslator($this->defaultTranslator);
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
        // @codingStandardsIgnoreStart
        $this->assertMatchesRegularExpression('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testRendersErrorMessagesUsingUnorderedListTranslated()
    {
        $mockTranslator = $this->createMock('Laminas\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(3))
            ->method('translate')
            ->willReturnOnConsecutiveCalls(
                'Translated first error message',
                'Translated second error message',
                'Translated third error message'
            )
        ;

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $this->helper->setTranslatorTextDomain('default');

        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element);
        // @codingStandardsIgnoreStart
        $this->assertMatchesRegularExpression('#<ul>\s*<li>Translated first error message</li>\s*<li>Translated second error message</li>\s*<li>Translated third error message</li>\s*</ul>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testRendersErrorMessagesWithoutDoubleTranslation()
    {
        $form = new Form('test_form');
        $form->add([
            'name' => 'test_element',
            'type' => Element\Color::class,
        ]);
        $form->setData(['test_element' => 'This is invalid!']);

        $mockValidatorTranslator = $this->createMock('Laminas\Validator\Translator\TranslatorInterface');
        $mockValidatorTranslator
            ->expects(self::once())
            ->method('translate')
            ->willReturnCallback(
                function ($message) {
                    self::assertEquals(
                        'The input does not match against pattern \'%pattern%\'',
                        $message,
                        'Unexpected translation key.'
                    );

                    return 'TRANSLATED: The input does not match against pattern \'%pattern%\'';
                }
            );

        AbstractValidator::setDefaultTranslator($mockValidatorTranslator, 'default');

        self::assertFalse($form->isValid());

        $mockFormTranslator = $this->createMock('Laminas\I18n\Translator\Translator');
        $mockFormTranslator
            ->expects(self::never())
            ->method('translate');

        $this->helper->setTranslator($mockFormTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $this->helper->setTranslatorTextDomain('default');

        // Disable translation...
        $this->helper->setTranslateMessages(false);

        $markup = $this->helper->render($form->get('test_element'));

        $this->assertMatchesRegularExpression('#^<ul>\s*<li>TRANSLATED#s', $markup);
    }

    public function testCanSpecifyAttributesForOpeningTag()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
    }

    public function testCanSpecifyAttributesForOpeningTagUsingInvoke()
    {
        $helper = $this->helper;
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $helper($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
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
        // @codingStandardsIgnoreStart
        $this->assertMatchesRegularExpression('#<div class="error">\s*<span>First error message</span>\s*<span>Second error message</span>\s*<span>Third error message</span>\s*</div>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testSpecifiedAttributesOverrideDefaults()
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);
        $element->setAttributes(['class' => 'foo']);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
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
        // @codingStandardsIgnoreStart
        $this->assertMatchesRegularExpression('#<ul class="error">\s*<li>First validator message</li>\s*<li>Second validator first message</li>\s*<li>Second validator second message</li>\s*</ul>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testCallingTheHelperToRenderInvokeCanReturnObject()
    {
        $helper = $this->helper;
        $this->assertEquals($helper(), $helper);
    }
}
