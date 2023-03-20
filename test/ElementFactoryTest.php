<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Generator;
use Laminas\Form\ElementFactory;
use LaminasTest\Form\TestAsset\ArgumentRecorder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ElementFactoryTest extends TestCase
{
    /** @return Generator<string, array{0: array|null, 1: array}> */
    public static function validCreationOptions(): Generator
    {
        yield 'array' => [['key' => 'value'], ['key' => 'value']];
        yield 'empty-array' => [[], []];
        yield 'null' => [null, []];
    }

    /**
     * @dataProvider validCreationOptions
     */
    public function testValidCreationOptions(array|null $creationOptions, array $expectedValue): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new ElementFactory();
        $result    = $factory->__invoke($container, ArgumentRecorder::class, $creationOptions);
        self::assertInstanceOf(ArgumentRecorder::class, $result);
        self::assertSame(['argumentrecorder', $expectedValue], $result->args);
    }
}
