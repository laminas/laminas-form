<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\View\Helper;

use Laminas\Captcha;
use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormElement as FormElementHelper;
use Laminas\Form\View\HelperConfig;
use Laminas\View\Helper\Doctype;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;

use function get_class;
use function substr_count;

class FormElementTest extends TestCase
{
    public $helper;
    public $renderer;

    protected function setUp(): void
    {
        $this->helper = new FormElementHelper();

        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function getInputElements()
    {
        return [
            ['text'],
            ['password'],
            ['checkbox'],
            ['radio'],
            ['submit'],
            ['reset'],
            ['file'],
            ['hidden'],
            ['image'],
            ['button'],
            ['number'],
            ['range'],
            ['date'],
            ['color'],
            ['search'],
            ['tel'],
            ['email'],
            ['url'],
            ['datetime'],
            ['datetime-local'],
            ['month'],
            ['week'],
            ['time'],
        ];
    }

    /**
     * @dataProvider getInputElements
     */
    public function testRendersExpectedInputElement($type)
    {
        if ($type === 'radio') {
            $element = new Element\Radio('foo');
        } elseif ($type === 'checkbox') {
            $element = new Element\Checkbox('foo');
        } elseif ($type === 'select') {
            $element = new Element\Select('foo');
        } else {
            $element = new Element('foo');
        }

        $element->setAttribute('type', $type);
        $element->setAttribute('options', ['option' => 'value']);
        $element->setAttribute('src', 'http://zend.com/img.png');
        $markup  = $this->helper->render($element);

        $this->assertStringContainsString('<input', $markup);
        $this->assertStringContainsString('type="' . $type . '"', $markup);
    }

    public function getMultiElements()
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
    public function testRendersMultiElementsAsExpected($type, $inputType, $additionalMarkup)
    {
        if ($type === 'radio') {
            $element = new Element\Radio('foo');
            $this->assertEquals('radio', $element->getAttribute('type'));
        } elseif ($type === 'multi_checkbox') {
            $element = new Element\MultiCheckbox('foo');
            $this->assertEquals('multi_checkbox', $element->getAttribute('type'));
        } elseif ($type === 'select') {
            $element = new Element\Select('foo');
            $this->assertEquals('select', $element->getAttribute('type'));
        } else {
            $element = new Element('foo');
        }
        $element->setAttribute('type', $type);
        $element->setValueOptions([
            'value1' => 'option',
            'value2' => 'label',
            'value3' => 'last',
        ]);
        $element->setAttribute('value', 'value2');
        $markup  = $this->helper->render($element);

        $this->assertEquals(3, substr_count($markup, '<' . $inputType), $markup);
        $this->assertStringContainsString($additionalMarkup, $markup);
        if ($type == 'select') {
            $this->assertMatchesRegularExpression('#value="value2"[^>]*?(selected="selected")#', $markup);
        }
    }

    public function testRendersCaptchaAsExpected()
    {
        $captcha = new Captcha\Dumb();
        $element = new Element\Captcha('foo');
        $element->setCaptcha($captcha);
        $markup = $this->helper->render($element);

        $this->assertStringContainsString($captcha->getLabel(), $markup);
    }

    public function testRendersCsrfAsExpected()
    {
        $element   = new Element\Csrf('foo');
        $inputSpec = $element->getInputSpecification();
        $hash = '';

        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            switch ($class) {
                case 'Laminas\Validator\Csrf':
                    $hash = $validator->getHash();
                    break;
                default:
                    break;
            }
        }

        $markup    = $this->helper->render($element);

        $this->assertMatchesRegularExpression('#<input[^>]*(type="hidden")#', $markup);
        $this->assertMatchesRegularExpression('#<input[^>]*(value="' . $hash . '")#', $markup);
    }

    public function testRendersTextareaAsExpected()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'textarea');
        $element->setAttribute('value', 'Initial content');
        $markup  = $this->helper->render($element);

        $this->assertStringContainsString('<textarea', $markup);
        $this->assertStringContainsString('>Initial content<', $markup);
    }

    public function testRendersCollectionAsExpected()
    {
        $element = new Element\Collection();
        $element->setLabel('foo');

        $markup  = $this->helper->render($element);
        $this->assertStringContainsString('<legend>foo</legend>', $markup);
    }

    public function testRendersButtonAsExpected()
    {
        $element = new Element\Button('foo');
        $element->setLabel('My Button');
        $markup  = $this->helper->render($element);

        $this->assertStringContainsString('<button', $markup);
        $this->assertStringContainsString('>My Button<', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
