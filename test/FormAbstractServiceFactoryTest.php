<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Filter\FilterPluginManager;
use Laminas\Form\FormAbstractServiceFactory;
use Laminas\Form\FormElementManager;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\Hydrator\HydratorPluginManager;
use Laminas\Validator\ValidatorPluginManager;
use PHPUnit_Framework_TestCase as TestCase;

class FormAbstractServiceFactoryTest extends TestCase
{
    public function setUp()
    {
        $services     = $this->services = new ServiceManager;
        $elements     = new FormElementManager;
        $filters      = new FilterPluginManager;
        $hydrators    = new HydratorPluginManager;
        $inputFilters = new InputFilterPluginManager;
        $validators   = new ValidatorPluginManager;

        $elements->setServiceLocator($services);
        $filters->setServiceLocator($services);
        $hydrators->setServiceLocator($services);
        $inputFilters->setServiceLocator($services);
        $validators->setServiceLocator($services);

        $services->setService('FilterManager', $filters);
        $services->setService('FormElementManager', $elements);
        $services->setService('HydratorManager', $hydrators);
        $services->setService('InputFilterManager', $inputFilters);
        $services->setService('ValidatorManager', $validators);

        $inputFilters->setInvokableClass('FooInputFilter', 'Laminas\InputFilter\InputFilter');

        $forms = $this->forms = new FormAbstractServiceFactory;
        $services->addAbstractFactory($forms);
    }

    public function testMissingConfigServiceIndicatesCannotCreateForm()
    {
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'foo', 'foo'));
    }

    public function testMissingFormServicePrefixIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array());
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'foo', 'foo'));
    }

    public function testMissingFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array());
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Form\Foo', 'Form\Foo'));
    }

    public function testInvalidFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array('forms' => 'string'));
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Form\Foo', 'Form\Foo'));
    }

    public function testEmptyFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array('forms' => array()));
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Form\Foo', 'Form\Foo'));
    }

    public function testMissingFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array(
            'forms' => array(
                'Bar' => array(),
            ),
        ));
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Form\Foo', 'Form\Foo'));
    }

    public function testInvalidFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array(
            'forms' => array(
                'Foo' => 'string',
            ),
        ));
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Foo', 'Foo'));
    }

    public function testEmptyFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('Config', array(
            'forms' => array(
                'Foo' => array(),
            ),
        ));
        $this->assertFalse($this->forms->canCreateServiceWithName($this->services, 'Foo', 'Foo'));
    }

    public function testPopulatedFormConfigIndicatesFormCanBeCreated()
    {
        $this->services->setService('Config', array(
            'forms' => array(
                'Foo' => array(
                    'type'     => 'Laminas\Form\Form',
                    'elements' => array(),
                ),
            ),
        ));
        $this->assertTrue($this->forms->canCreateServiceWithName($this->services, 'Foo', 'Foo'));
    }

    public function testFormCanBeCreatedViaInteractionOfAllManagers()
    {
        $formConfig = array(
            'hydrator' => 'ObjectProperty',
            'type'     => 'Laminas\Form\Form',
            'elements' => array(
                array(
                    'spec' => array(
                        'type' => 'Laminas\Form\Element\Email',
                        'name' => 'email',
                        'options' => array(
                            'label' => 'Your email address',
                        )
                    ),
                ),
            ),
            'input_filter' => 'FooInputFilter',
        );
        $config = array('forms' => array('Foo' => $formConfig));
        $this->services->setService('Config', $config);
        $form = $this->forms->createServiceWithName($this->services, 'Foo', 'Foo');
        $this->assertInstanceOf('Laminas\Form\Form', $form);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf('Laminas\InputFilter\InputFilter', $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $this->assertInstanceOf('Laminas\InputFilter\Factory', $inputFactory);
        $filters      = $this->services->get('FilterManager');
        $validators   = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }

    public function testFormCanBeCreatedViaInteractionOfAllManagersExceptInputFilterManager()
    {
        $formConfig = array(
            'hydrator' => 'ObjectProperty',
            'type'     => 'Laminas\Form\Form',
            'elements' => array(
                array(
                    'spec' => array(
                        'type' => 'Laminas\Form\Element\Email',
                        'name' => 'email',
                        'options' => array(
                            'label' => 'Your email address',
                        )
                    ),
                ),
            ),
            'input_filter' => array(
                'email' => array(
                    'required'   => true,
                    'filters'    => array(
                        array(
                            'name' => 'string_trim',
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'email_address',
                        ),
                    ),
                ),
            ),
        );
        $config = array('forms' => array('Foo' => $formConfig));
        $this->services->setService('Config', $config);
        $form = $this->forms->createServiceWithName($this->services, 'Foo', 'Foo');
        $this->assertInstanceOf('Laminas\Form\Form', $form);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf('Laminas\InputFilter\InputFilter', $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $filters      = $this->services->get('FilterManager');
        $validators   = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }
}
