<?php

namespace LaminasTest\Form\Integration;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\InitializerInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

use function method_exists;

class ServiceManagerTest extends TestCase
{
    use ProphecyTrait;

    public function testInitInitializerShouldBeCalledAfterAllOtherInitializers()
    {
        // Reproducing the behaviour of a full stack MVC + ModuleManager
        $serviceManagerConfig = new Config([
            'factories' => [
                'FormElementManager' => FormElementManagerFactory::class,
            ],
        ]);

        $serviceManager = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($serviceManager);

        $formElementManager = $serviceManager->get('FormElementManager');

        $test = 0;
        $spy  = function () use (&$test) {
            TestCase::assertEquals(1, $test);
        };

        $element = $this->prophesize(Element::class);
        $element->init()->will($spy);

        $initializer   = $this->prophesize(InitializerInterface::class);
        $incrementTest = function () use (&$test) {
            $test += 1;
        };

        if (method_exists($serviceManager, 'configure')) {
            $initializer->__invoke(
                $serviceManager,
                $element->reveal()
            )->will($incrementTest)->shouldBeCalled();
        } else {
            $initializer->initialize(
                $element->reveal(),
                $formElementManager
            )->will($incrementTest)->shouldBeCalled();
        }

        $formElementManagerConfig = new Config([
            'factories'    => [
                'InitializableElement' => function () use ($element) {
                    return $element->reveal();
                },
            ],
            'initializers' => [
                $initializer->reveal(),
            ],
        ]);

        $formElementManagerConfig->configureServiceManager($formElementManager);

        $formElementManager->get('InitializableElement');
    }

    public function testInjectFactoryInitializerShouldTriggerBeforeInitInitializer()
    {
        // Reproducing the behaviour of a full stack MVC + ModuleManager
        $serviceManagerConfig = new Config([
            'factories' => [
                'FormElementManager' => FormElementManagerFactory::class,
            ],
        ]);

        $serviceManager = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($serviceManager);

        $formElementManager = $serviceManager->get('FormElementManager');

        $initializer                 = $this->prophesize(InitializerInterface::class);
        $formElementManagerAssertion = function ($form) use ($formElementManager) {
            TestCase::assertInstanceOf(Form::class, $form);
            TestCase::assertSame($formElementManager, $form->getFormFactory()->getFormElementManager());
            return true;
        };
        if (method_exists($serviceManager, 'configure')) {
            $initializer->__invoke(
                $serviceManager,
                Argument::that($formElementManagerAssertion)
            )->shouldBeCalled();
        } else {
            $initializer->initialize(
                Argument::that($formElementManagerAssertion),
                $formElementManager
            )->shouldBeCalled();
        }

        $formElementManagerConfig = new Config([
            'factories'    => [
                'MyForm' => function () {
                    return new TestAsset\Form();
                },
            ],
            'initializers' => [
                $initializer->reveal(),
            ],
        ]);

        $formElementManagerConfig->configureServiceManager($formElementManager);

        /** @var TestAsset\Form */
        $form = $formElementManager->get('MyForm');
        $this->assertSame($formElementManager, $form->elementManagerAtInit);
    }
}
