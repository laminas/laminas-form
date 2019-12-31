<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Filter;
use Laminas\Form;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\Hydrator\HydratorPluginManager;
use PHPUnit_Framework_TestCase as TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var FormFactory
     */
    protected $factory;

    public function setUp()
    {
        $elementManager = new FormElementManager();
        $elementManager->setServiceLocator(new ServiceManager());
        $this->factory = new FormFactory($elementManager);
    }

    public function testCanCreateElements()
    {
        $element = $this->factory->createElement(array(
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\ElementInterface', $element);
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('text', $element->getAttribute('type'));
        $this->assertEquals('foo-class', $element->getAttribute('class'));
        $this->assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanCreateFieldsets()
    {
        $fieldset = $this->factory->createFieldset(array(
            'name'       => 'foo',
            'object'     => 'LaminasTest\Form\TestAsset\Model',
            'attributes' => array(
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $fieldset);
        $this->assertEquals('foo', $fieldset->getName());
        $this->assertEquals('fieldset', $fieldset->getAttribute('type'));
        $this->assertEquals('foo-class', $fieldset->getAttribute('class'));
        $this->assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
        $this->assertEquals(new \LaminasTest\Form\TestAsset\Model, $fieldset->getObject());
    }

    public function testCanCreateFieldsetsWithElements()
    {
        $fieldset = $this->factory->createFieldset(array(
            'name'       => 'foo',
            'elements' => array(
                array(
                    'flags' => array(
                        'name' => 'bar',
                    ),
                    'spec' => array(
                        'attributes' => array(
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'flags' => array(
                        'name' => 'baz',
                    ),
                    'spec' => array(
                        'attributes' => array(
                            'type' => 'radio',
                            'options' => array(
                                'foo' => 'Foo Bar',
                                'bar' => 'Bar Baz',
                            ),
                        ),
                    ),
                ),
                array(
                    'flags' => array(
                        'priority' => 10,
                    ),
                    'spec' => array(
                        'name'       => 'bat',
                        'attributes' => array(
                            'type' => 'textarea',
                            'content' => 'Type here...',
                        ),
                    ),
                ),
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $fieldset);
        $elements = $fieldset->getElements();
        $this->assertEquals(3, count($elements));
        $this->assertTrue($fieldset->has('bar'));
        $this->assertTrue($fieldset->has('baz'));
        $this->assertTrue($fieldset->has('bat'));

        $element = $fieldset->get('bar');
        $this->assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        $this->assertEquals('radio', $element->getAttribute('type'));
        $this->assertEquals(array(
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ), $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        $this->assertEquals('textarea', $element->getAttribute('type'));
        $this->assertEquals('Type here...', $element->getAttribute('content'));
        $this->assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        $this->assertEquals('bat', $test);
    }

    public function testCanCreateNestedFieldsets()
    {
        $masterFieldset = $this->factory->createFieldset(array(
            'name'       => 'foo',
            'fieldsets'  => array(
                array(
                    'flags' => array('name' => 'bar'),
                    'spec'  => array(
                        'elements' => array(
                            array(
                                'flags' => array(
                                    'name' => 'bar',
                                ),
                                'spec' => array(
                                    'attributes' => array(
                                        'type' => 'text',
                                    ),
                                ),
                            ),
                            array(
                                'flags' => array(
                                    'name' => 'baz',
                                ),
                                'spec' => array(
                                    'attributes' => array(
                                        'type' => 'radio',
                                        'options' => array(
                                            'foo' => 'Foo Bar',
                                            'bar' => 'Bar Baz',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'flags' => array(
                                    'priority' => 10,
                                ),
                                'spec' => array(
                                    'name'       => 'bat',
                                    'attributes' => array(
                                        'type' => 'textarea',
                                        'content' => 'Type here...',
                                    ),
                                ),
                            ),
                        ),
                    )
                )
            )
        ));
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $masterFieldset);
        $fieldsets = $masterFieldset->getFieldsets();
        $this->assertEquals(1, count($fieldsets));
        $this->assertTrue($masterFieldset->has('bar'));

        $fieldset = $masterFieldset->get('bar');
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $fieldset);

        $element = $fieldset->get('bar');
        $this->assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        $this->assertEquals('radio', $element->getAttribute('type'));
        $this->assertEquals(array(
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ), $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        $this->assertEquals('textarea', $element->getAttribute('type'));
        $this->assertEquals('Type here...', $element->getAttribute('content'));
        $this->assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        $this->assertEquals('bat', $test);
    }

    public function testCanCreateForms()
    {
        $form = $this->factory->createForm(array(
            'name'       => 'foo',
            'object'     => 'LaminasTest\Form\TestAsset\Model',
            'attributes' => array(
                'method' => 'get',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $this->assertEquals('foo', $form->getName());
        $this->assertEquals('get', $form->getAttribute('method'));
        $this->assertEquals(new \LaminasTest\Form\TestAsset\Model, $form->getObject());
    }

    public function testCanCreateFormsWithNamedInputFilters()
    {
        $form = $this->factory->createForm(array(
            'name'         => 'foo',
            'input_filter' => 'LaminasTest\Form\TestAsset\InputFilter',
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $filter = $form->getInputFilter();
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\InputFilter', $filter);
    }

    public function testCanCreateFormsWithInputFilterSpecifications()
    {
        $form = $this->factory->createForm(array(
            'name'         => 'foo',
            'input_filter' => array(
                'foo' => array(
                    'name'       => 'foo',
                    'required'   => false,
                    'validators' => array(
                        array(
                            'name' => 'not_empty',
                        ),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'min' => 3,
                                'max' => 5,
                            ),
                        ),
                    ),
                ),
                'bar' => array(
                    'allow_empty' => true,
                    'filters'     => array(
                        array(
                            'name' => 'string_trim',
                        ),
                        array(
                            'name' => 'string_to_lower',
                            'options' => array(
                                'encoding' => 'ISO-8859-1',
                            ),
                        ),
                    ),
                ),
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $filter = $form->getInputFilter();
        $this->assertInstanceOf('Laminas\InputFilter\InputFilterInterface', $filter);
        $this->assertEquals(2, count($filter));
        foreach (array('foo', 'bar') as $name) {
            $input = $filter->get($name);

            switch ($name) {
                case 'foo':
                    $this->assertInstanceOf('Laminas\InputFilter\Input', $input);
                    $this->assertFalse($input->isRequired());
                    $this->assertEquals(2, count($input->getValidatorChain()));
                    break;
                case 'bar':
                    $this->assertInstanceOf('Laminas\InputFilter\Input', $input);
                    $this->assertTrue($input->allowEmpty());
                    $this->assertEquals(2, count($input->getFilterChain()));
                    break;
                default:
                    $this->fail('Unexpected input named "' . $name . '" found in input filter');
            }
        }
    }

    public function testCanCreateFormsWithInputFilterInstances()
    {
        $filter = new TestAsset\InputFilter();
        $form = $this->factory->createForm(array(
            'name'         => 'foo',
            'input_filter' => $filter,
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $test = $form->getInputFilter();
        $this->assertSame($filter, $test);
    }

    public function testCanCreateFormsAndSpecifyHydrator()
    {
        $form = $this->factory->createForm(array(
            'name'     => 'foo',
            'hydrator' => 'Laminas\Stdlib\Hydrator\ObjectProperty',
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanCreateFormsAndSpecifyHydratorManagedByHydratorManager()
    {
        $hydrators = new HydratorPluginManager();
        $services = $this->factory->getFormElementManager()->getServiceLocator();
        $hydrators->setServiceLocator($services);
        $services->setService('HydratorManager', new HydratorPluginManager());

        $form = $this->factory->createForm(array(
            'name'     => 'foo',
            'hydrator' => 'ObjectProperty',
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanCreateHydratorFromArray()
    {
        $form = $this->factory->createForm(array(
            'name' => 'foo',
            'hydrator' => array(
                'type' => 'Laminas\Stdlib\Hydrator\ClassMethods',
                'options' => array('underscoreSeparatedKeys' => false),
            ),
        ));

        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ClassMethods', $hydrator);
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testCanCreateHydratorFromConcreteClass()
    {
        $form = $this->factory->createForm(array(
            'name' => 'foo',
            'hydrator' => new \Laminas\Stdlib\Hydrator\ObjectProperty()
        ));

        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanCreateFormsAndSpecifyFactory()
    {
        $form = $this->factory->createForm(array(
            'name'    => 'foo',
            'factory' => 'Laminas\Form\Factory',
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $factory = $form->getFormFactory();
        $this->assertInstanceOf('Laminas\Form\Factory', $factory);
    }

    public function testCanCreateFactoryFromArray()
    {
        $form = $this->factory->createForm(array(
            'name'    => 'foo',
            'factory' => array(
                'type' => 'Laminas\Form\Factory',
            ),
        ));

        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $factory = $form->getFormFactory();
        $this->assertInstanceOf('Laminas\Form\Factory', $factory);
    }

    public function testCanCreateFactoryFromConcreteClass()
    {
        $factory = new \Laminas\Form\Factory();
        $form = $this->factory->createForm(array(
            'name'    => 'foo',
            'factory' => $factory,
        ));

        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $test = $form->getFormFactory();
        $this->assertSame($factory, $test);
    }

    public function testCanCreateFormFromConcreteClassAndSpecifyCustomValidatorByName()
    {
        $validatorManager = new \Laminas\Validator\ValidatorPluginManager();
        $validatorManager->setInvokableClass('baz', 'Laminas\Validator\Digits');

        $defaultValidatorChain = new \Laminas\Validator\ValidatorChain();
        $defaultValidatorChain->setPluginManager($validatorManager);

        $inputFilterFactory = new \Laminas\InputFilter\Factory();
        $inputFilterFactory->setDefaultValidatorChain($defaultValidatorChain);

        $factory = new FormFactory();
        $factory->setInputFilterFactory($inputFilterFactory);

        $form = $factory->createForm(array(
            'name'         => 'foo',
            'factory'      => $factory,
            'input_filter' => array(
                'bar' => array(
                    'name'       => 'bar',
                    'required'   => true,
                    'validators' => array(
                        array(
                            'name' => 'baz',
                        ),
                    ),
                ),
            ),
        ));

        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);

        $inputFilter = $form->getInputFilter();
        $this->assertInstanceOf('Laminas\InputFilter\InputFilterInterface', $inputFilter);

        $input = $inputFilter->get('bar');
        $this->assertInstanceOf('Laminas\InputFilter\Input', $input);

        $validatorChain = $input->getValidatorChain();
        $this->assertInstanceOf('Laminas\Validator\ValidatorChain', $validatorChain);

        $validatorArray = $validatorChain->getValidators();
        $found = false;
        foreach ($validatorArray as $validator) {
            $validatorInstance = $validator['instance'];
            $this->assertInstanceOf('Laminas\Validator\ValidatorInterface', $validatorInstance);

            if ($validatorInstance instanceof \Laminas\Validator\Digits) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCanCreateFormWithHydratorAndInputFilterAndElementsAndFieldsets()
    {
        $form = $this->factory->createForm(array(
            'name'       => 'foo',
            'elements' => array(
                array(
                    'flags' => array(
                        'name' => 'bar',
                    ),
                    'spec' => array(
                        'attributes' => array(
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'flags' => array(
                        'name' => 'baz',
                    ),
                    'spec' => array(
                        'attributes' => array(
                            'type' => 'radio',
                            'options' => array(
                                'foo' => 'Foo Bar',
                                'bar' => 'Bar Baz',
                            ),
                        ),
                    ),
                ),
                array(
                    'flags' => array(
                        'priority' => 10,
                    ),
                    'spec' => array(
                        'name'       => 'bat',
                        'attributes' => array(
                            'type' => 'textarea',
                            'content' => 'Type here...',
                        ),
                    ),
                ),
            ),
            'fieldsets'  => array(
                array(
                    'flags' => array('name' => 'foobar'),
                    'spec'  => array(
                        'elements' => array(
                            array(
                                'flags' => array(
                                    'name' => 'bar',
                                ),
                                'spec' => array(
                                    'attributes' => array(
                                        'type' => 'text',
                                    ),
                                ),
                            ),
                            array(
                                'flags' => array(
                                    'name' => 'baz',
                                ),
                                'spec' => array(
                                    'attributes' => array(
                                        'type' => 'radio',
                                        'options' => array(
                                            'foo' => 'Foo Bar',
                                            'bar' => 'Bar Baz',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'flags' => array(
                                    'priority' => 10,
                                ),
                                'spec' => array(
                                    'name'       => 'bat',
                                    'attributes' => array(
                                        'type' => 'textarea',
                                        'content' => 'Type here...',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'input_filter' => 'LaminasTest\Form\TestAsset\InputFilter',
            'hydrator'     => 'Laminas\Stdlib\Hydrator\ObjectProperty',
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);

        $elements = $form->getElements();
        $this->assertEquals(3, count($elements));
        $this->assertTrue($form->has('bar'));
        $this->assertTrue($form->has('baz'));
        $this->assertTrue($form->has('bat'));

        $element = $form->get('bar');
        $this->assertEquals('text', $element->getAttribute('type'));

        $element = $form->get('baz');
        $this->assertEquals('radio', $element->getAttribute('type'));
        $this->assertEquals(array(
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ), $element->getAttribute('options'));

        $element = $form->get('bat');
        $this->assertEquals('textarea', $element->getAttribute('type'));
        $this->assertEquals('Type here...', $element->getAttribute('content'));
        $this->assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($form as $element) {
            $test = $element->getName();
            break;
        }
        $this->assertEquals('bat', $test);

        // Test against nested fieldset
        $fieldsets = $form->getFieldsets();
        $this->assertEquals(1, count($fieldsets));
        $this->assertTrue($form->has('foobar'));

        $fieldset = $form->get('foobar');
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $fieldset);

        $element = $fieldset->get('bar');
        $this->assertEquals('text', $element->getAttribute('type'));

        $element = $fieldset->get('baz');
        $this->assertEquals('radio', $element->getAttribute('type'));
        $this->assertEquals(array(
            'foo' => 'Foo Bar',
            'bar' => 'Bar Baz',
        ), $element->getAttribute('options'));

        $element = $fieldset->get('bat');
        $this->assertEquals('textarea', $element->getAttribute('type'));
        $this->assertEquals('Type here...', $element->getAttribute('content'));
        $this->assertEquals('bat', $element->getName());

        // Testing that priority flag is present and works as expected
        foreach ($fieldset as $element) {
            $test = $element->getName();
            break;
        }
        $this->assertEquals('bat', $test);

        // input filter
        $filter = $form->getInputFilter();
        $this->assertInstanceOf('LaminasTest\Form\TestAsset\InputFilter', $filter);

        // hydrator
        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanCreateFormUsingCreate()
    {
        $form = $this->factory->create(array(
            'type'       => 'Laminas\Form\Form',
            'name'       => 'foo',
            'attributes' => array(
                'method' => 'get',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);
        $this->assertEquals('foo', $form->getName());
        $this->assertEquals('get', $form->getAttribute('method'));
    }

    public function testCanCreateFieldsetUsingCreate()
    {
        $fieldset = $this->factory->create(array(
            'type'       => 'Laminas\Form\Fieldset',
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FieldsetInterface', $fieldset);
        $this->assertEquals('foo', $fieldset->getName());
        $this->assertEquals('fieldset', $fieldset->getAttribute('type'));
        $this->assertEquals('foo-class', $fieldset->getAttribute('class'));
        $this->assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
    }

    public function testCanCreateElementUsingCreate()
    {
        $element = $this->factory->create(array(
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\ElementInterface', $element);
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('text', $element->getAttribute('type'));
        $this->assertEquals('foo-class', $element->getAttribute('class'));
        $this->assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testAutomaticallyAddFieldsetTypeWhenCreateFieldset()
    {
        $fieldset = $this->factory->createFieldset(array('name' => 'myFieldset'));
        $this->assertInstanceOf('Laminas\Form\Fieldset', $fieldset);
        $this->assertEquals('myFieldset', $fieldset->getName());
    }

    public function testAutomaticallyAddFormTypeWhenCreateForm()
    {
        $form = $this->factory->createForm(array('name' => 'myForm'));
        $this->assertInstanceOf('Laminas\Form\Form', $form);
        $this->assertEquals('myForm', $form->getName());
    }

    public function testCanPullHydratorThroughServiceManager()
    {
        $serviceLocator = $this->factory->getFormElementManager()->getServiceLocator();
        $serviceLocator->setInvokableClass('MyHydrator', 'Laminas\Stdlib\Hydrator\ObjectProperty');

        $fieldset = $this->factory->createFieldset(array(
            'hydrator' => 'MyHydrator',
            'name' => 'fieldset',
            'elements' => array(
                array(
                    'flags' => array(
                        'name' => 'bar',
                    ),
                )
            )
        ));

        $this->assertInstanceOf('Laminas\Stdlib\Hydrator\ObjectProperty', $fieldset->getHydrator());
    }

    public function testCreatedFieldsetsHaveFactoryAndFormElementManagerInjected()
    {
        $fieldset = $this->factory->createFieldset(array('name' => 'myFieldset'));
        $this->assertAttributeInstanceOf('Laminas\Form\Factory', 'factory', $fieldset);
        $this->assertSame($fieldset->getFormFactory()->getFormElementManager(), $this->factory->getFormElementManager());
    }

    /**
     * @group 6949
     */
    public function testPrepareAndInjectWillThrowAndException()
    {
        $fieldset = $this->factory->createFieldset(array('name' => 'myFieldset'));

        $this->setExpectedException('Laminas\Form\Exception\DomainException');
        $this->factory->configureFieldset($fieldset, array('hydrator' => 0));
    }

    public function testCanCreateFormWithNullElements()
    {
        $form = $this->factory->createForm(array(
            'name' => 'foo',
            'elements' => array(
                'bar' => array(
                    'spec' => array(
                        'name' => 'bar',
                    ),
                ),
                'baz' => null,
                'bat' => array(
                    'spec' => array(
                        'name' => 'bat',
                    ),
                ),
            ),
        ));
        $this->assertInstanceOf('Laminas\Form\FormInterface', $form);

        $elements = $form->getElements();
        $this->assertEquals(2, count($elements));
        $this->assertTrue($form->has('bar'));
        $this->assertFalse($form->has('baz'));
        $this->assertTrue($form->has('bat'));
    }
}
