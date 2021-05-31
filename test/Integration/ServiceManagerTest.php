<?php

declare(strict_types=1);

namespace LaminasTest\Form\Integration;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class ServiceManagerTest extends TestCase
{
    use ProphecyTrait;

    public function testInitInitializerShouldBeCalledAfterAllOtherInitializers(): void
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
        $spy  = function () use (&$test): void {
            TestCase::assertEquals(1, $test);
        };

        $element = $this->prophesize(Element::class);
        $element->init()->will($spy);

        $initializer   = $this->prophesize(InitializerInterface::class);
        $incrementTest = function () use (&$test): void {
            $test += 1;
        };

        $initializer->__invoke(
            $serviceManager,
            $element->reveal()
        )->will($incrementTest)->shouldBeCalled();

        $formElementManagerConfig = new Config([
            'factories'    => [
                'InitializableElement' => static function () use ($element): Element {
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

    public function testInjectFactoryInitializerShouldTriggerBeforeInitInitializer(): void
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
        $formElementManagerAssertion = static function ($form) use ($formElementManager): bool {
            TestCase::assertInstanceOf(Form::class, $form);
            TestCase::assertSame($formElementManager, $form->getFormFactory()->getFormElementManager());
            return true;
        };
        $initializer->__invoke(
            $serviceManager,
            Argument::that($formElementManagerAssertion)
        )->shouldBeCalled();

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

        /** @var TestAsset\Form $form */
        $form = $formElementManager->get('MyForm');
        $this->assertSame($formElementManager, $form->elementManagerAtInit);
    }
}
