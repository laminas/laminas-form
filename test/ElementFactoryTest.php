<?php
/**
 * @see       https://github.com/zendframework/zend-form for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-form/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Form;

use ArrayObject;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Zend\Form\ElementFactory;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\Form\TestAsset\ArgumentRecorder;

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
