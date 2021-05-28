<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper\Factory;

use Generator;
use Laminas\Form\Element;
use Laminas\Form\View\Helper\Factory\FormElementErrorsFactory;
use Laminas\I18n\Translator\TranslatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_map;
use function explode;
use function implode;
use function sprintf;
use function str_replace;
use function ucfirst;

class FormElementErrorsFactoryTest extends TestCase
{
    public function testFactoryShouldCreateHelperWithoutConfigService(): void
    {
        // Create test double for container
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(false);

        // Create factory
        $factory    = new FormElementErrorsFactory();
        $viewHelper = $factory($container);

        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider configProvider
     */
    public function testFactoryShouldCreateHelperAndSetOptions(
        array $config,
        array $result
    ): void {
        // Create test double for container
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($config);

        // Create factory
        $factory    = new FormElementErrorsFactory();
        $viewHelper = $factory($container);

        // Test
        foreach ($result as $option => $value) {
            if ($option === 'translate_messages') {
                continue;
            }

            $methodName = 'get' . implode(
                array_map(
                    static function ($value) {
                        return ucfirst($value);
                    },
                    explode('_', $option)
                )
            );

            self::assertSame(
                $value,
                $viewHelper->$methodName()
            );
        }
    }

    /**
     * @dataProvider configProvider
     */
    public function testFactoryShouldCreateHelperWithTranslateOption(
        array $config,
        array $result
    ): void {
        // Create test double for container
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($config);

        // Create test double for translator
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('translate')->willReturn('tranlated message');

        // Create factory
        $factory    = new FormElementErrorsFactory();
        $viewHelper = $factory($container);
        $viewHelper->setTranslator($translator);

        // Prepare error messages
        $errorMessage = 'error message';
        if ($result['translate_messages'] === true) {
            $errorMessage = 'tranlated message';
        }
        $expected = sprintf(
            '%s%s%s',
            str_replace('%s', '', $result['message_open_format']),
            $errorMessage,
            $result['message_close_string']
        );

        // Test
        self::assertSame(
            $expected,
            $viewHelper->render((new Element())->setMessages([$errorMessage]))
        );
    }

    public function configProvider(): Generator
    {
        $defaultOptions = [
            'message_open_format'      => '<ul%s><li>',
            'message_close_string'     => '</li></ul>',
            'message_separator_string' => '</li><li>',
            'attributes'               => [],
            'translate_messages'       => true,
        ];
        $customOptions  = [
            'message_open_format'      => '<div>',
            'message_close_string'     => '</div>',
            'message_separator_string' => '',
            'attributes'               => ['class' => 'test'],
            'translate_messages'       => false,
        ];

        yield 'empty-config' => [
            [],
            $defaultOptions,
        ];
        yield 'empty-view-helper-config' => [
            [
                'view_helper_config' => [],
            ],
            $defaultOptions,
        ];
        yield 'empty-form-element-errors-config' => [
            [
                'view_helper_config' => [],
            ],
            $defaultOptions,
        ];
        yield 'custom-config' => [
            [
                'view_helper_config' => [
                    'form_element_errors' => $customOptions,
                ],
            ],
            $customOptions,
        ];
    }
}
