<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormInterface;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\Digits;
use Laminas\Validator\ValidatorChain;
use Laminas\Validator\ValidatorInterface;
use Laminas\Validator\ValidatorPluginManager;
use LaminasTest\Form\TestAsset\InputFilter;
use LaminasTest\Form\TestAsset\Model;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    private FormFactory $factory;

    private ServiceManager $services;

    protected function setUp(): void
    {
        $this->services = new ServiceManager();
        $elementManager = new FormElementManager($this->services);
        $this->factory  = new FormFactory($elementManager);
    }

    public function testCanCreateElements(): void
    {
        $element = $this->factory->createElement([
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ],
        ]);
        self::assertInstanceOf(ElementInterface::class, $element);
        self::assertEquals('foo', $element->getName());
        self::assertEquals('text', $element->getAttribute('type'));
        self::assertEquals('foo-class', $element->getAttribute('class'));
        self::assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanCreateFieldsets(): void
    {
        $fieldset = $this->factory->createFieldset([
            'name'       => 'foo',
            'object'     => Model::class,
            'attributes' => [
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ],
        ]);
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);
        self::assertEquals('foo', $fieldset->getName());
        self::assertEquals('fieldset', $fieldset->getAttribute('type'));
        self::assertEquals('foo-class', $fieldset->getAttribute('class'));
        self::assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
        self::assertEquals(new Model(), $fieldset->getObject());
    }

    public function testCanCreateFieldsetsWithElements(): void
    {
        $fieldset = $this->factory->createFieldset([
            'name'     => 'foo',
            'elements' => [
                [
                    'flags' => [
                        'name' => 'bar',
                    ],
                    'spec'  => [
                        'attributes' => [
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'baz',
                    ],
                    'spec'  => [
                        'attributes' => [
                            'type'    => 'radio',
                            'options' => [
                                'foo' => 'Foo Bar',
                                'bar' => 'Bar Baz',
                            ],
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'priority' => 10,
                    ],
                    'spec'  => [
                        'name'       => 'bat',
                        'attributes' => [
                            'type'    => 'textarea',
                            'content' => 'Type here...',
                        ],
                    ],
                ],
            ],
        ]);
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);
        $elements = $fieldset->getElements();
        self::assertCount(3, $elements);
        self::assertTrue($fieldset->has('bar'));
        self::assertTrue($fieldset->has('baz'));
        self::assertTrue($fieldset->has('bat'));

        $element = $fieldset->get('bar');
        self::assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        self::assertEquals('radio', $element->getAttribute('type'));
        self::assertEquals([
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ], $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        self::assertEquals('textarea', $element->getAttribute('type'));
        self::assertEquals('Type here...', $element->getAttribute('content'));
        self::assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        self::assertEquals('bat', $test);
    }

    public function testCanCreateNestedFieldsets(): void
    {
        $masterFieldset = $this->factory->createFieldset([
            'name'      => 'foo',
            'fieldsets' => [
                [
                    'flags' => ['name' => 'bar'],
                    'spec'  => [
                        'elements' => [
                            [
                                'flags' => [
                                    'name' => 'bar',
                                ],
                                'spec'  => [
                                    'attributes' => [
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'name' => 'baz',
                                ],
                                'spec'  => [
                                    'attributes' => [
                                        'type'    => 'radio',
                                        'options' => [
                                            'foo' => 'Foo Bar',
                                            'bar' => 'Bar Baz',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'priority' => 10,
                                ],
                                'spec'  => [
                                    'name'       => 'bat',
                                    'attributes' => [
                                        'type'    => 'textarea',
                                        'content' => 'Type here...',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        self::assertInstanceOf(FieldsetInterface::class, $masterFieldset);
        $fieldsets = $masterFieldset->getFieldsets();
        self::assertCount(1, $fieldsets);
        self::assertTrue($masterFieldset->has('bar'));

        $fieldset = $masterFieldset->get('bar');
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);

        $element = $fieldset->get('bar');
        self::assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        self::assertEquals('radio', $element->getAttribute('type'));
        self::assertEquals([
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ], $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        self::assertEquals('textarea', $element->getAttribute('type'));
        self::assertEquals('Type here...', $element->getAttribute('content'));
        self::assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        self::assertEquals('bat', $test);
    }

    public function testCanCreateForms(): void
    {
        $form = $this->factory->createForm([
            'name'       => 'foo',
            'object'     => Model::class,
            'attributes' => [
                'method' => 'get',
            ],
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertEquals('foo', $form->getName());
        self::assertEquals('get', $form->getAttribute('method'));
        self::assertEquals(new Model(), $form->getObject());
    }

    public function testCanCreateFormsWithNamedInputFilters(): void
    {
        $form = $this->factory->createForm([
            'name'         => 'foo',
            'input_filter' => InputFilter::class,
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        $filter = $form->getInputFilter();
        self::assertInstanceOf(InputFilter::class, $filter);
    }

    public function testCanCreateFormsWithInputFilterSpecifications(): void
    {
        $form = $this->factory->createForm([
            'name'         => 'foo',
            'input_filter' => [
                'foo' => [
                    'name'       => 'foo',
                    'required'   => false,
                    'validators' => [
                        [
                            'name' => 'NotEmpty',
                        ],
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 3,
                                'max' => 5,
                            ],
                        ],
                    ],
                ],
                'bar' => [
                    'allow_empty' => true,
                    'filters'     => [
                        [
                            'name' => 'StringTrim',
                        ],
                        [
                            'name'    => 'StringToLower',
                            'options' => [
                                'encoding' => 'ISO-8859-1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        $filter = $form->getInputFilter();
        self::assertInstanceOf(InputFilterInterface::class, $filter);
        self::assertCount(2, $filter);
        foreach (['foo', 'bar'] as $name) {
            $input = $filter->get($name);

            switch ($name) {
                case 'foo':
                    self::assertInstanceOf(Input::class, $input);
                    self::assertFalse($input->isRequired());
                    self::assertCount(2, $input->getValidatorChain());
                    break;
                case 'bar':
                    self::assertInstanceOf(Input::class, $input);
                    self::assertTrue($input->allowEmpty());
                    self::assertCount(2, $input->getFilterChain());
                    break;
            }
        }
    }

    public function testCanCreateFormsWithInputFilterInstances(): void
    {
        $filter = new InputFilter();
        $form   = $this->factory->createForm([
            'name'         => 'foo',
            'input_filter' => $filter,
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        $test = $form->getInputFilter();
        self::assertSame($filter, $test);
    }

    public function testCanCreateFormsAndSpecifyHydrator(): void
    {
        $form = $this->factory->createForm([
            'name'     => 'foo',
            'hydrator' => ObjectPropertyHydrator::class,
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        $hydrator = $form->getHydrator();
        self::assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);
    }

    public function testCanCreateFormsAndSpecifyHydratorManagedByHydratorManager(): void
    {
        $hydratorShortName = 'ObjectPropertyHydrator';
        $hydratorType      = ObjectPropertyHydrator::class;

        $this->services->setService(HydratorPluginManager::class, new HydratorPluginManager($this->services));

        $form = $this->factory->createForm([
            'name'     => 'foo',
            'hydrator' => $hydratorShortName,
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        $hydrator = $form->getHydrator();
        self::assertInstanceOf($hydratorType, $hydrator);
    }

    public function testCanCreateHydratorFromArray(): void
    {
        $form = $this->factory->createForm([
            'name'     => 'foo',
            'hydrator' => [
                'type'    => ClassMethodsHydrator::class,
                'options' => ['underscoreSeparatedKeys' => false],
            ],
        ]);

        self::assertInstanceOf(\Laminas\Form\Form::class, $form);
        $hydrator = $form->getHydrator();

        self::assertInstanceOf(ClassMethodsHydrator::class, $hydrator);
        self::assertFalse($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testCanCreateHydratorFromConcreteClass(): void
    {
        $form = $this->factory->createForm([
            'name'     => 'foo',
            'hydrator' => new ObjectPropertyHydrator(),
        ]);

        self::assertInstanceOf(FormInterface::class, $form);
        $hydrator = $form->getHydrator();
        self::assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);
    }

    public function testCanCreateFormsAndSpecifyFactory(): void
    {
        $form = $this->factory->createForm([
            'name'    => 'foo',
            'factory' => FormFactory::class,
        ]);
        self::assertInstanceOf(\Laminas\Form\Form::class, $form);
        $factory = $form->getFormFactory();
        self::assertInstanceOf(FormFactory::class, $factory);
    }

    public function testCanCreateFactoryFromArray(): void
    {
        $form = $this->factory->createForm([
            'name'    => 'foo',
            'factory' => [
                'type' => FormFactory::class,
            ],
        ]);

        self::assertInstanceOf(\Laminas\Form\Form::class, $form);
        $factory = $form->getFormFactory();
        self::assertInstanceOf(FormFactory::class, $factory);
    }

    public function testCanCreateFactoryFromConcreteClass(): void
    {
        $factory = new FormFactory();
        $form    = $this->factory->createForm([
            'name'    => 'foo',
            'factory' => $factory,
        ]);

        self::assertInstanceOf(\Laminas\Form\Form::class, $form);
        $test = $form->getFormFactory();
        self::assertSame($factory, $test);
    }

    public function testCanCreateFormFromConcreteClassAndSpecifyCustomValidatorByName(): void
    {
        $validatorManager = new ValidatorPluginManager($this->services);
        $validatorManager->setInvokableClass('baz', Digits::class);

        $defaultValidatorChain = new ValidatorChain();
        $defaultValidatorChain->setPluginManager($validatorManager);

        $inputFilterFactory = new Factory();
        $inputFilterFactory->setDefaultValidatorChain($defaultValidatorChain);

        $factory = new FormFactory();
        $factory->setInputFilterFactory($inputFilterFactory);

        $form = $factory->createForm([
            'name'         => 'foo',
            'factory'      => $factory,
            'input_filter' => [
                'bar' => [
                    'name'       => 'bar',
                    'required'   => true,
                    'validators' => [
                        [
                            'name' => 'baz',
                        ],
                    ],
                ],
            ],
        ]);

        self::assertInstanceOf(FormInterface::class, $form);

        $inputFilter = $form->getInputFilter();
        self::assertInstanceOf(InputFilterInterface::class, $inputFilter);

        $input = $inputFilter->get('bar');
        self::assertInstanceOf(Input::class, $input);

        $validatorChain = $input->getValidatorChain();
        self::assertInstanceOf(ValidatorChain::class, $validatorChain);

        $validatorArray = $validatorChain->getValidators();
        $found          = false;
        foreach ($validatorArray as $validator) {
            $validatorInstance = $validator['instance'];
            self::assertInstanceOf(ValidatorInterface::class, $validatorInstance);

            if ($validatorInstance instanceof Digits) {
                $found = true;
                break;
            }
        }
        self::assertTrue($found);
    }

    // @codingStandardsIgnoreLine
    public function testCanCreateFormFromConcreteClassWithCustomValidatorByNameAndInputFilterFactoryInConstructor(): void
    {
        $validatorManager = new ValidatorPluginManager($this->services);
        $validatorManager->setInvokableClass('baz', Digits::class);

        $defaultValidatorChain = new ValidatorChain();
        $defaultValidatorChain->setPluginManager($validatorManager);

        $inputFilterFactory = new Factory();
        $inputFilterFactory->setDefaultValidatorChain($defaultValidatorChain);

        $factory = new FormFactory(null, $inputFilterFactory);

        $form = $factory->createForm([
            'name'         => 'foo',
            'factory'      => $factory,
            'input_filter' => [
                'bar' => [
                    'name'       => 'bar',
                    'required'   => true,
                    'validators' => [
                        [
                            'name' => 'baz',
                        ],
                    ],
                ],
            ],
        ]);

        self::assertInstanceOf(FormInterface::class, $form);

        $inputFilter = $form->getInputFilter();
        self::assertInstanceOf(InputFilterInterface::class, $inputFilter);

        $input = $inputFilter->get('bar');
        self::assertInstanceOf(Input::class, $input);

        $validatorChain = $input->getValidatorChain();
        self::assertInstanceOf(ValidatorChain::class, $validatorChain);

        $validatorArray = $validatorChain->getValidators();
        $found          = false;
        foreach ($validatorArray as $validator) {
            $validatorInstance = $validator['instance'];
            self::assertInstanceOf(ValidatorInterface::class, $validatorInstance);

            if ($validatorInstance instanceof Digits) {
                $found = true;
                break;
            }
        }
        self::assertTrue($found);
    }

    public function testCanCreateFormWithHydratorAndInputFilterAndElementsAndFieldsets(): void
    {
        $form = $this->factory->createForm([
            'name'         => 'foo',
            'elements'     => [
                [
                    'flags' => [
                        'name' => 'bar',
                    ],
                    'spec'  => [
                        'attributes' => [
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'baz',
                    ],
                    'spec'  => [
                        'attributes' => [
                            'type'    => 'radio',
                            'options' => [
                                'foo' => 'Foo Bar',
                                'bar' => 'Bar Baz',
                            ],
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'priority' => 10,
                    ],
                    'spec'  => [
                        'name'       => 'bat',
                        'attributes' => [
                            'type'    => 'textarea',
                            'content' => 'Type here...',
                        ],
                    ],
                ],
            ],
            'fieldsets'    => [
                [
                    'flags' => ['name' => 'foobar'],
                    'spec'  => [
                        'elements' => [
                            [
                                'flags' => [
                                    'name' => 'bar',
                                ],
                                'spec'  => [
                                    'attributes' => [
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'name' => 'baz',
                                ],
                                'spec'  => [
                                    'attributes' => [
                                        'type'    => 'radio',
                                        'options' => [
                                            'foo' => 'Foo Bar',
                                            'bar' => 'Bar Baz',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'priority' => 10,
                                ],
                                'spec'  => [
                                    'name'       => 'bat',
                                    'attributes' => [
                                        'type'    => 'textarea',
                                        'content' => 'Type here...',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'input_filter' => InputFilter::class,
            'hydrator'     => ObjectPropertyHydrator::class,
        ]);
        self::assertInstanceOf(FormInterface::class, $form);

        $elements = $form->getElements();
        self::assertCount(3, $elements);
        self::assertTrue($form->has('bar'));
        self::assertTrue($form->has('baz'));
        self::assertTrue($form->has('bat'));

        $element = $form->get('bar');
        self::assertEquals('text', $element->getAttribute('type'));

        $element = $form->get('baz');
        self::assertEquals('radio', $element->getAttribute('type'));
        self::assertEquals([
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ], $element->getAttribute('options'));

        $element = $form->get('bat');
        self::assertEquals('textarea', $element->getAttribute('type'));
        self::assertEquals('Type here...', $element->getAttribute('content'));
        self::assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($form as $element) {
            $test = $element->getName();
            break;
        }
        self::assertEquals('bat', $test);

        // Test against nested fieldset
        $fieldsets = $form->getFieldsets();
        self::assertCount(1, $fieldsets);
        self::assertTrue($form->has('foobar'));

        $fieldset = $form->get('foobar');
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);

        $element = $fieldset->get('bar');
        self::assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        self::assertEquals('radio', $element->getAttribute('type'));
        self::assertEquals([
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ], $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        self::assertEquals('textarea', $element->getAttribute('type'));
        self::assertEquals('Type here...', $element->getAttribute('content'));
        self::assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        self::assertEquals('bat', $test);

        // input filter
        $filter = $form->getInputFilter();
        self::assertInstanceOf(InputFilter::class, $filter);

        // hydrator
        $hydrator = $form->getHydrator();
        self::assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);
    }

    public function testCanCreateFormUsingCreate(): void
    {
        $form = $this->factory->create([
            'type'       => \Laminas\Form\Form::class,
            'name'       => 'foo',
            'attributes' => [
                'method' => 'get',
            ],
        ]);
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertEquals('foo', $form->getName());
        self::assertEquals('get', $form->getAttribute('method'));
    }

    public function testCanCreateFieldsetUsingCreate(): void
    {
        $fieldset = $this->factory->create([
            'type'       => Fieldset::class,
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ],
        ]);
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);
        self::assertEquals('foo', $fieldset->getName());
        self::assertEquals('fieldset', $fieldset->getAttribute('type'));
        self::assertEquals('foo-class', $fieldset->getAttribute('class'));
        self::assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
    }

    public function testCanCreateElementUsingCreate(): void
    {
        $element = $this->factory->create([
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ],
        ]);
        self::assertInstanceOf(ElementInterface::class, $element);
        self::assertEquals('foo', $element->getName());
        self::assertEquals('text', $element->getAttribute('type'));
        self::assertEquals('foo-class', $element->getAttribute('class'));
        self::assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testAutomaticallyAddFieldsetTypeWhenCreateFieldset(): void
    {
        $fieldset = $this->factory->createFieldset(['name' => 'myFieldset']);
        self::assertInstanceOf(Fieldset::class, $fieldset);
        self::assertEquals('myFieldset', $fieldset->getName());
    }

    public function testAutomaticallyAddFormTypeWhenCreateForm(): void
    {
        $form = $this->factory->createForm(['name' => 'myForm']);
        self::assertInstanceOf(\Laminas\Form\Form::class, $form);
        self::assertEquals('myForm', $form->getName());
    }

    public function testCanPullHydratorThroughServiceManager(): void
    {
        $this->services->setInvokableClass('MyHydrator', ObjectPropertyHydrator::class);

        $fieldset = $this->factory->createFieldset([
            'hydrator' => 'MyHydrator',
            'name'     => 'fieldset',
            'elements' => [
                [
                    'flags' => [
                        'name' => 'bar',
                    ],
                ],
            ],
        ]);

        self::assertInstanceOf(ObjectPropertyHydrator::class, $fieldset->getHydrator());
    }

    public function testCreatedFieldsetsHaveFactoryAndFormElementManagerInjected(): void
    {
        $fieldset = $this->factory->createFieldset(['name' => 'myFieldset']);
        self::assertInstanceOf(Fieldset::class, $fieldset);
        self::assertSame(
            $fieldset->getFormFactory()->getFormElementManager(),
            $this->factory->getFormElementManager()
        );
    }

    /**
     * @group issue-6949
     */
    public function testPrepareAndInjectWillThrowAndException(): void
    {
        $fieldset = $this->factory->createFieldset(['name' => 'myFieldset']);

        $this->expectException(DomainException::class);
        $this->factory->configureFieldset($fieldset, ['hydrator' => 0]);
    }

    public function testCanCreateFormWithNullElements(): void
    {
        $form = $this->factory->createForm([
            'name'     => 'foo',
            'elements' => [
                'bar' => [
                    'spec' => [
                        'name' => 'bar',
                    ],
                ],
                'baz' => null,
                'bat' => [
                    'spec' => [
                        'name' => 'bat',
                    ],
                ],
            ],
        ]);
        self::assertInstanceOf(FormInterface::class, $form);

        $elements = $form->getElements();
        self::assertCount(2, $elements);
        self::assertTrue($form->has('bar'));
        self::assertFalse($form->has('baz'));
        self::assertTrue($form->has('bat'));
    }

    public function testCanCreateWithConstructionLogicInOptions(): void
    {
        $formManager = $this->factory->getFormElementManager();
        $formManager->setFactory(
            TestAsset\FieldsetWithDependency::class,
            TestAsset\FieldsetWithDependencyFactory::class
        );

        $collection = $this->factory->create([
            'type'    => Form\Element\Collection::class,
            'name'    => 'my_fieldset_collection',
            'options' => [
                'target_element' => [
                    'type' => TestAsset\FieldsetWithDependency::class,
                ],
            ],
        ]);

        self::assertInstanceOf(Form\Element\Collection::class, $collection);

        $targetElement = $collection->getTargetElement();

        self::assertInstanceOf(TestAsset\FieldsetWithDependency::class, $targetElement);
        self::assertInstanceOf(InputFilter::class, $targetElement->getDependency());
    }
}
