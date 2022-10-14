<?php

declare(strict_types=1);

namespace LaminasTest\Form\Integration;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ServiceManagerTest extends TestCase
{
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
        self::assertInstanceOf(FormElementManager::class, $formElementManager);

        $element     = new class extends Element {
        };
        $initializer = new class implements InitializerInterface {
            public function __invoke(ContainerInterface $container, mixed $instance)
            {
                TestCase::assertInstanceOf(Element::class, $instance);
                $instance->setName('special');
            }
        };

        $formElementManagerConfig = new Config([
            'factories'    => [
                'InitializableElement' => static fn(): Element => $element,
            ],
            'initializers' => [
                $initializer,
            ],
        ]);

        $formElementManagerConfig->configureServiceManager($formElementManager);

        self::assertNull($element->getName());
        $formElementManager->get('InitializableElement');
        self::assertSame('special', $element->getName());
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
        self::assertInstanceOf(FormElementManager::class, $formElementManager);

        $initializer = new class ($formElementManager) implements InitializerInterface
        {
            public bool $initialized = false;

            public function __construct(private FormElementManager $manager)
            {
            }

            public function __invoke(ContainerInterface $container, mixed $instance)
            {
                TestCase::assertInstanceOf(Form::class, $instance);
                TestCase::assertSame($this->manager, $instance->getFormFactory()->getFormElementManager());
                $this->initialized = true;
            }
        };

        $formElementManagerConfig = new Config([
            'factories'    => [
                'MyForm' => static fn(): \LaminasTest\Form\Integration\TestAsset\Form => new TestAsset\Form(),
            ],
            'initializers' => [
                $initializer,
            ],
        ]);

        $formElementManagerConfig->configureServiceManager($formElementManager);

        $form = $formElementManager->get('MyForm');
        self::assertInstanceOf(TestAsset\Form::class, $form);
        self::assertSame($formElementManager, $form->elementManagerAtInit);
        self::assertTrue($initializer->initialized);
    }
}
