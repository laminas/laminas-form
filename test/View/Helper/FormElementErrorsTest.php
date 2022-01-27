<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\View\Helper\FormElementErrors as FormElementErrorsHelper;
use Laminas\I18n\Translator\Translator;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Translator\TranslatorInterface;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @property FormElementErrorsHelper $helper
 */
final class FormElementErrorsTest extends AbstractCommonTestCase
{
    use ProphecyTrait;

    /** @var null|TranslatorInterface */
    protected $defaultTranslator;

    protected function setUp(): void
    {
        $this->defaultTranslator = AbstractValidator::getDefaultTranslator();
        $this->helper            = new FormElementErrorsHelper();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        AbstractValidator::setDefaultTranslator($this->defaultTranslator);
    }

    public function getMessageList(): array
    {
        return [
            'First error message',
            'Second error message',
            'Third error message',
        ];
    }

    public function testLackOfMessagesResultsInEmptyMarkup(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertEquals('', $markup);
    }

    public function testRendersErrorMessagesUsingUnorderedListByDefault(): void
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element);
        // @codingStandardsIgnoreStart
        $this->assertMatchesRegularExpression('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
        // @codingStandardsIgnoreEnd
    }

    public function testRendersErrorMessagesUsingUnorderedListTranslated(): void
    {
        $mockTranslator = $this->createMock(Translator::class);
        $mockTranslator->expects($this->exactly(3))
            ->method('translate')
            ->willReturnOnConsecutiveCalls(
                'Translated first error message',
                'Translated second error message',
                'Translated third error message'
            );

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

    public function testRendersErrorMessagesWithoutDoubleTranslation(): void
    {
        $form = new Form('test_form');
        $form->add([
            'name' => 'test_element',
            'type' => Element\Color::class,
        ]);
        $form->setData(['test_element' => 'This is invalid!']);

        $mockValidatorTranslator = $this->createMock(TranslatorInterface::class);
        $mockValidatorTranslator
            ->expects(self::once())
            ->method('translate')
            ->willReturnCallback(
                static function (string $message): string {
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

        $mockFormTranslator = $this->createMock(Translator::class);
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

    public function testCanSpecifyAttributesForOpeningTag(): void
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
    }

    public function testCanSpecifyAttributesForOpeningTagUsingInvoke(): void
    {
        $helper   = $this->helper;
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $helper($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
    }

    public function testCanSpecifyAlternateMarkupStringsViaSetters(): void
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

    public function testSpecifiedAttributesOverrideDefaults(): void
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);
        $element->setAttributes(['class' => 'foo']);

        $markup = $this->helper->render($element, ['class' => 'error']);
        $this->assertStringContainsString('ul class="error"', $markup);
    }

    public function testGetAttributes(): void
    {
        $messages = $this->getMessageList();
        $element  = new Element('foo');
        $element->setMessages($messages);

        $this->helper->setAttributes(['class' => 'error']);

        $this->helper->render($element);

        $this->assertEquals(['class' => 'error'], $this->helper->getAttributes());
    }

    public function testRendersNestedMessageSetsAsAFlatList(): void
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

    public function testCallingTheHelperToRenderInvokeCanReturnObject(): void
    {
        $helper = $this->helper;
        $this->assertEquals($helper(), $helper);
    }

    public function testHtmlEscapingOfMessages(): void
    {
        $messages = [
            [
                '<span>First validator message</span>',
                '<span>Second validator first message</span>',
                '<span>Second validator second message</span>',
            ],
        ];
        $element  = new Element('foo');
        $element->setMessages($messages);

        $markup = $this->helper->render($element);

        $this->assertStringNotContainsString('<span>', $markup);
        $this->assertStringNotContainsString('</span>', $markup);
    }
}
