<?php

namespace LaminasTest\Form;

use Laminas\Filter\FilterPluginManager;
use Laminas\Form\Element\Email;
use Laminas\Form\Form;
use Laminas\Form\FormAbstractServiceFactory;
use Laminas\Form\FormElementManager;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use PHPUnit\Framework\TestCase;

use function class_exists;

class FormAbstractServiceFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->objectPropertyHydratorClass = class_exists(ObjectPropertyHydrator::class)
            ? ObjectPropertyHydrator::class
            : ObjectProperty::class;

        $services     = $this->services = new ServiceManager();
        $elements     = new FormElementManager($services);
        $filters      = new FilterPluginManager($services);
        $hydrators    = new HydratorPluginManager($services);
        $inputFilters = new InputFilterPluginManager($services);
        $validators   = new ValidatorPluginManager($services);

        $services->setService('FilterManager', $filters);
        $services->setService('FormElementManager', $elements);
        $services->setService('HydratorManager', $hydrators);
        $services->setService('InputFilterManager', $inputFilters);
        $services->setService('ValidatorManager', $validators);

        $inputFilters->setInvokableClass('FooInputFilter', InputFilter::class);

        $forms = $this->forms = new FormAbstractServiceFactory($services);
        $services->addAbstractFactory($forms);
    }

    public function testMissingConfigServiceIndicatesCannotCreateForm()
    {
        $this->assertFalse($this->forms->canCreate($this->services, 'foo'));
    }

    public function testMissingFormServicePrefixIndicatesCannotCreateForm()
    {
        $this->services->setService('config', []);
        $this->assertFalse($this->forms->canCreate($this->services, 'foo'));
    }

    public function testMissingFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', []);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testInvalidFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', ['forms' => 'string']);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testEmptyFormManagerConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', ['forms' => []]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testMissingFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', [
            'forms' => [
                'Bar' => [],
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testInvalidFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', [
            'forms' => [
                'Foo' => 'string',
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Foo'));
    }

    public function testEmptyFormConfigIndicatesCannotCreateForm()
    {
        $this->services->setService('config', [
            'forms' => [
                'Foo' => [],
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Foo'));
    }

    public function testPopulatedFormConfigIndicatesFormCanBeCreated()
    {
        $this->services->setService('config', [
            'forms' => [
                'Foo' => [
                    'type'     => Form::class,
                    'elements' => [],
                ],
            ],
        ]);
        $this->assertTrue($this->forms->canCreate($this->services, 'Foo'));
    }

    public function testFormCanBeCreatedViaInteractionOfAllManagers()
    {
        $formConfig = [
            'hydrator'     => class_exists(ObjectPropertyHydrator::class)
                ? 'ObjectPropertyHydrator'
                : 'ObjectProperty',
            'type'         => Form::class,
            'elements'     => [
                [
                    'spec' => [
                        'type'    => Email::class,
                        'name'    => 'email',
                        'options' => [
                            'label' => 'Your email address',
                        ],
                    ],
                ],
            ],
            'input_filter' => 'FooInputFilter',
        ];
        $config     = ['forms' => ['Foo' => $formConfig]];
        $this->services->setService('config', $config);
        $form = $this->forms->__invoke($this->services, 'Foo');
        $this->assertInstanceOf(Form::class, $form);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->objectPropertyHydratorClass, $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $this->assertInstanceOf(Factory::class, $inputFactory);
        $filters    = $this->services->get('FilterManager');
        $validators = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }

    public function testFormCanBeCreatedViaInteractionOfAllManagersExceptInputFilterManager()
    {
        $formConfig = [
            'hydrator'     => class_exists(ObjectPropertyHydrator::class)
                ? 'ObjectPropertyHydrator'
                : 'ObjectProperty',
            'type'         => Form::class,
            'elements'     => [
                [
                    'spec' => [
                        'type'    => Email::class,
                        'name'    => 'email',
                        'options' => [
                            'label' => 'Your email address',
                        ],
                    ],
                ],
            ],
            'input_filter' => [
                'email' => [
                    'required'   => true,
                    'filters'    => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                    'validators' => [
                        [
                            'name' => 'EmailAddress',
                        ],
                    ],
                ],
            ],
        ];
        $config     = ['forms' => ['Foo' => $formConfig]];
        $this->services->setService('config', $config);
        $form = $this->forms->__invoke($this->services, 'Foo');
        $this->assertInstanceOf(Form::class, $form);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf($this->objectPropertyHydratorClass, $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $filters      = $this->services->get('FilterManager');
        $validators   = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }
}
