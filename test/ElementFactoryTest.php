<?php

namespace LaminasTest\Form;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\Form\TestAsset\ArgumentRecorder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function uniqid;

use const PHP_INT_MAX;

class ElementFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function validCreationOptions()
    {
        yield 'ArrayObject' => [new ArrayObject(['key' => 'value']), ['key' => 'value']];
        yield 'array' => [['key' => 'value'], ['key' => 'value']];
        yield 'empty-array' => [[], []];
        yield 'null' => [null, []];
    }

    /**
     * @dataProvider validCreationOptions
     *
     * @param mixed $creationOptions
     * @param array $expectedValue
     */
    public function testValidCreationOptions($creationOptions, array $expectedValue)
    {
        $container = $this->prophesize(ServiceLocatorInterface::class)
            ->willImplement(ContainerInterface::class)
            ->reveal();

        $factory = new ElementFactory($creationOptions);
        $result = $factory->createService($container, ArgumentRecorder::class);
        $this->assertInstanceOf(ArgumentRecorder::class, $result);
        $this->assertSame(['argumentrecorder', $expectedValue], $result->args);
    }

    public function invalidCreationOptions()
    {
        yield 'object' => [(object) ['key' => 'val']];
        yield 'int' => [PHP_INT_MAX];
        yield 'string' => [uniqid('', true)];
    }

    /**
     * @dataProvider invalidCreationOptions
     *
     * @param $creationOptions
     */
    public function testInvalidCreationOptionsException($creationOptions)
    {
        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage('cannot use non-array, non-traversable, non-null creation options;');
        new ElementFactory($creationOptions);
    }
}
