<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use ArrayObject;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Service\DiAbstractServiceFactoryFactory;
use Laminas\Mvc\Service\DiFactory;
use Laminas\Mvc\Service\DiServiceInitializerFactory;
use Laminas\Mvc\Service\FormElementManagerFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container as SessionContainer;
use Laminas\Validator\Csrf;
use PHPUnit_Framework_TestCase as TestCase;

class FormElementManagerFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var \Laminas\Mvc\Controller\ControllerManager
     */
    protected $loader;

    public function setUp()
    {
        $this->markTestSkipped('This test needs to be moved to the laminas-mvc package');

        /*
        $formElementManagerFactory = new FormElementManagerFactory();
        $config = new ArrayObject(array('di' => array()));
        $services = $this->services = new ServiceManager();
        $services->setService('Laminas\ServiceManager\ServiceLocatorInterface', $services);
        $services->setFactory('FormElementManager', $formElementManagerFactory);
        $services->setService('Config', $config);
        $services->setFactory('Di', new DiFactory());
        $services->setFactory('DiAbstractServiceFactory', new DiAbstractServiceFactoryFactory());
        $services->setFactory('DiServiceInitializer', new DiServiceInitializerFactory());

        $this->manager = $services->get('FormElementManager');

        $this->standaloneManager = new FormElementManager();
         */
    }

    public function tearDown()
    {
        /*
        $ref = new \ReflectionClass('Laminas\Validator\Csrf');
        $hashCache = $ref->getProperty('hashCache');
        $hashCache->setAccessible(true);
        $hashCache->setValue(new Csrf, array());
        SessionContainer::setDefaultManager(null);
         */
    }

    public function testWillInstantiateFormFromInvokable()
    {
        $form = $this->manager->get('form');
        $this->assertInstanceof('Laminas\Form\Form', $form);
    }

    public function testWillInstantiateFormFromDiAbstractFactory()
    {
        //without DiAbstractFactory
        $this->assertFalse($this->standaloneManager->has('LaminasTest\Form\TestAsset\CustomForm'));
        //with DiAbstractFactory
        $this->assertTrue($this->manager->has('LaminasTest\Form\TestAsset\CustomForm'));
        $form = $this->manager->get('LaminasTest\Form\TestAsset\CustomForm');
        $this->assertInstanceof('LaminasTest\Form\TestAsset\CustomForm', $form);
    }

    public function testNoWrapFieldName()
    {
        $form = $this->manager->get('LaminasTest\Form\TestAsset\CustomForm');
        $this->assertFalse($form->wrapElements(), 'ensure wrapElements option');
        $this->assertTrue($form->has('email'), 'ensure the form has email element');
        $emailElement = $form->get('email');
        $this->assertEquals('email', $emailElement->getName());
    }

    public function testCsrfCompatibility()
    {
        $_SESSION = array();
        $formClass = 'LaminasTest\Form\TestAsset\CustomForm';
        $ref = new \ReflectionClass('Laminas\Validator\Csrf');
        $hashPropRef = $ref->getProperty('hash');
        $hashPropRef->setAccessible(true);
        //check bare born
        $preForm = new $formClass;
        $csrf = $preForm->get('csrf')->getCsrfValidator();
        $this->assertNull($hashPropRef->getValue($csrf), 'Test "new Form" has no hash');
        //check FormElementManager
        $postForm = $this->manager->get($formClass);
        $postCsrf = $postForm->get('csrf')->getCsrfValidator();
        $this->assertNull($hashPropRef->getValue($postCsrf), 'Test "form from FormElementManager" has no hash');
    }

    public function testCsrfWorkFlow()
    {
        $_SESSION = array();
        $formClass = 'LaminasTest\Form\TestAsset\CustomForm';

        $preForm = new $formClass;
        $preForm->prepare();
        $requestHash = $preForm->get('csrf')->getValue();

        $postForm = $this->manager->get($formClass);
        $postCsrf = $postForm->get('csrf')->getCsrfValidator();

        $this->assertTrue($postCsrf->isValid($requestHash), 'Test csrf validation');
    }
}
