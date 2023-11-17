<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Captcha;
use Laminas\Form\ConfigProvider;
use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormElement as FormElementHelper;
use Laminas\Validator\Csrf;
use Laminas\View\Helper\Doctype;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;

use function substr_count;

final class FormElementTest extends TestCase
{
    /** @var FormElementHelper */
    public $helper;

    /** @var RendererInterface */
    public $renderer;

    protected function setUp(): void
    {
        Doctype::unsetDoctypeRegistry();

        $this->helper   = new FormElementHelper();
        $this->renderer = new PhpRenderer();

        $helperPluginManager = $this->renderer->getHelperPluginManager();
        $viewHelperConfig    = (new ConfigProvider())->getViewHelperConfig();
        $helperPluginManager->configure($viewHelperConfig);
        $this->renderer->setHelperPluginManager($helperPluginManager);

        $this->helper->setView($this->renderer);
    }

    /** @return array<string, array{0: string}> */
    public static function getInputElements(): array
    {
        return [
            'text'           => ['text'],
            'password'       => ['password'],
            'checkbox'       => ['checkbox'],
            'radio'          => ['radio'],
            'submit'         => ['submit'],
            'reset'          => ['reset'],
            'file'           => ['file'],
            'hidden'         => ['hidden'],
            'image'          => ['image'],
            'button'         => ['button'],
            'number'         => ['number'],
            'range'          => ['range'],
            'date'           => ['date'],
            'color'          => ['color'],
            'search'         => ['search'],
            'tel'            => ['tel'],
            'email'          => ['email'],
            'url'            => ['url'],
            'datetime'       => ['datetime'],
            'datetime-local' => ['datetime-local'],
            'month'          => ['month'],
            'week'           => ['week'],
            'time'           => ['time'],
        ];
    }

    /**
     * @dataProvider getInputElements
     */
    public function testRendersExpectedInputElement(string $type): void
    {
        $element = new Element('foo');

        if ($type === 'radio') {
            $element = new Element\Radio('foo');
            $element->setValueOptions(['option' => 'value']);
        }

        if ($type === 'checkbox') {
            $element = new Element\Checkbox('foo');
        }

        if ($type === 'select') {
            $element = new Element\Select('foo');
            $element->setValueOptions(['option' => 'value']);
        }

        if ($type === 'image') {
            $element->setAttribute('src', '/some-image.png');
        }

        $element->setAttribute('type', $type);
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<input', $markup);
        self::assertStringContainsString('type="' . $type . '"', $markup);
    }

    public static function getMultiElements(): array
    {
        return [
            ['radio', 'input', 'type="radio"'],
            ['multi_checkbox', 'input', 'type="checkbox"'],
            ['select', 'option', '<select'],
        ];
    }

    /**
     * @dataProvider getMultiElements
     * @group multi
     */
    public function testRendersMultiElementsAsExpected(string $type, string $inputType, string $additionalMarkup): void
    {
        if ($type === 'radio') {
            $element = new Element\Radio('foo');
            self::assertEquals('radio', $element->getAttribute('type'));
        } elseif ($type === 'multi_checkbox') {
            $element = new Element\MultiCheckbox('foo');
            self::assertEquals('multi_checkbox', $element->getAttribute('type'));
        } elseif ($type === 'select') {
            $element = new Element\Select('foo');
            self::assertEquals('select', $element->getAttribute('type'));
        } else {
            $element = new Element('foo');
        }
        $element->setAttribute('type', $type);
        if ($element instanceof Element\Select || $element instanceof Element\MultiCheckbox) {
            $element->setValueOptions([
                'value1' => 'option',
                'value2' => 'label',
                'value3' => 'last',
            ]);
        }
        $element->setAttribute('value', 'value2');
        $markup = $this->helper->render($element);

        self::assertEquals(3, substr_count($markup, '<' . $inputType), $markup);
        self::assertStringContainsString($additionalMarkup, $markup);
        if ($type === 'select') {
            self::assertMatchesRegularExpression('#value="value2"[^>]*?(selected="selected")#', $markup);
        }
    }

    public function testRendersCaptchaAsExpected(): void
    {
        $captcha = new Captcha\Dumb();
        $element = new Element\Captcha('foo');
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);

        self::assertStringContainsString($captcha->getLabel(), $markup);
    }

    public function testRendersCsrfAsExpected(): void
    {
        $element   = new Element\Csrf('foo');
        $inputSpec = $element->getInputSpecification();
        $hash      = '';

        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            switch ($class) {
                case Csrf::class:
                    $hash = $validator->getHash();
                    break;
                default:
                    break;
            }
        }

        $markup = $this->helper->render($element);

        self::assertMatchesRegularExpression('#<input[^>]*(type="hidden")#', $markup);
        self::assertMatchesRegularExpression('#<input[^>]*(value="' . $hash . '")#', $markup);
    }

    public function testRendersTextareaAsExpected(): void
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'textarea');
        $element->setAttribute('value', 'Initial content');
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<textarea', $markup);
        self::assertStringContainsString('>Initial content<', $markup);
    }

    public function testRendersCollectionAsExpected(): void
    {
        $element = new Element\Collection();
        $element->setLabel('foo');

        $markup = $this->helper->render($element);
        self::assertStringContainsString('<legend>foo</legend>', $markup);
    }

    public function testRendersButtonAsExpected(): void
    {
        $element = new Element\Button('foo');
        $element->setLabel('My Button');
        $markup = $this->helper->render($element);

        self::assertStringContainsString('<button', $markup);
        self::assertStringContainsString('>My Button<', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        $element = new Element('foo');
        self::assertSame($this->helper, $this->helper->__invoke());
    }
}
