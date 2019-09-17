<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Zend\Form\ElementFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\Form\TestAsset\ArgumentRecorder;

/**
 * @group      Zend_Form
 */
class FormElementManagerTest extends TestCase
{
    public function testCreationOptionsHandled()
    {
        $this->assertCreationOptionsOfServiceAreSet(['key' => 'value'], new ArrayObject(['key' => 'value']));
        $this->assertCreationOptionsOfServiceAreSet(['key' => 'value'], ['key' => 'value']);
        $this->assertCreationOptionsOfServiceAreSet([], []);
        $this->assertCreationOptionsOfServiceAreSet([], null);
    }

    private function assertCreationOptionsOfServiceAreSet($expectedValue, $input)
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class)->reveal();

        $factory = new ElementFactory($input);
        $result = $factory->createService($serviceLocator, ArgumentRecorder::class);
        $this->assertInstanceOf(ArgumentRecorder::class, $result);
        $this->assertSame(['argumentrecorder', $expectedValue], $result->args);
    }
}
