<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\Form\TestAsset\ArgumentRecorder;
use PHPUnit\Framework\TestCase;

use function uniqid;

use const PHP_INT_MAX;

class ElementFactoryTest extends TestCase
{
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
