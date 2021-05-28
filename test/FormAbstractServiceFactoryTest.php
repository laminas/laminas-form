<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Filter\FilterPluginManager;
use Laminas\Form\Element\Email;
use Laminas\Form\Form;
use Laminas\Form\FormAbstractServiceFactory;
use Laminas\Form\FormElementManager;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use PHPUnit\Framework\TestCase;

final class FormAbstractServiceFactoryTest extends TestCase
{
    /** @var ServiceManager */
    private $services;
    /** @var FormAbstractServiceFactory */
    private $forms;

    protected function setUp(): void
    {
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

        $forms = $this->forms = new FormAbstractServiceFactory();
        $services->addAbstractFactory($forms);
    }

    public function testMissingConfigServiceIndicatesCannotCreateForm(): void
    {
        $this->assertFalse($this->forms->canCreate($this->services, 'foo'));
    }

    public function testMissingFormServicePrefixIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', []);
        $this->assertFalse($this->forms->canCreate($this->services, 'foo'));
    }

    public function testMissingFormManagerConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', []);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testInvalidFormManagerConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', ['forms' => 'string']);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testEmptyFormManagerConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', ['forms' => []]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testMissingFormConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', [
            'forms' => [
                'Bar' => [],
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Form\Foo'));
    }

    public function testInvalidFormConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', [
            'forms' => [
                'Foo' => 'string',
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Foo'));
    }

    public function testEmptyFormConfigIndicatesCannotCreateForm(): void
    {
        $this->services->setService('config', [
            'forms' => [
                'Foo' => [],
            ],
        ]);
        $this->assertFalse($this->forms->canCreate($this->services, 'Foo'));
    }

    public function testPopulatedFormConfigIndicatesFormCanBeCreated(): void
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

    public function testFormCanBeCreatedViaInteractionOfAllManagers(): void
    {
        $formConfig = [
            'hydrator'     => 'ObjectPropertyHydrator',
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
        $this->assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $this->assertInstanceOf(Factory::class, $inputFactory);
        $filters    = $this->services->get('FilterManager');
        $validators = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }

    public function testFormCanBeCreatedViaInteractionOfAllManagersExceptInputFilterManager(): void
    {
        $formConfig = [
            'hydrator'     => 'ObjectPropertyHydrator',
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
        $this->assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $inputFilter);

        $inputFactory = $inputFilter->getFactory();
        $filters      = $this->services->get('FilterManager');
        $validators   = $this->services->get('ValidatorManager');
        $this->assertSame($filters, $inputFactory->getDefaultFilterChain()->getPluginManager());
        $this->assertSame($validators, $inputFactory->getDefaultValidatorChain()->getPluginManager());
    }
}
