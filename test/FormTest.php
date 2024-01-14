<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Factory;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;
use Laminas\Form\View\Helper\FormCollection;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\BaseInputFilter;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputInterface;
use LaminasTest\Form\TestAsset\Entity\Category;
use PHPUnit\Framework\TestCase;
use stdClass;

use function extension_loaded;
use function print_r;
use function spl_object_hash;
use function uniqid;
use function var_export;

final class FormTest extends TestCase
{
    private Form $form;

    protected function setUp(): void
    {
        $this->form = new Form();
    }

    private function getComposedEntity(): TestAsset\Entity\Address
    {
        $address = new TestAsset\Entity\Address();
        $address->setStreet('1 Rue des Champs Elysées');

        $city = new TestAsset\Entity\City();
        $city->setName('Paris');
        $city->setZipCode('75008');

        $country = new TestAsset\Entity\Country();
        $country->setName('France');
        $country->setContinent('Europe');

        $city->setCountry($country);
        $address->setCity($city);

        return $address;
    }

    private function getOneToManyEntity(): TestAsset\Entity\Product
    {
        $product = new TestAsset\Entity\Product();
        $product->setName('Chair');
        $product->setPrice(10);

        $firstCategory = new Category();
        $firstCategory->setName('Office');

        $secondCategory = new Category();
        $secondCategory->setName('Armchair');

        $product->setCategories([$firstCategory, $secondCategory]);

        return $product;
    }

    public function populateHydratorStrategyForm(): void
    {
        $this->form->add(new Element('entities'));
    }

    public function populateForm(): void
    {
        $this->form->add(new Element('foo'));
        $this->form->add(new Element('bar'));

        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->add(new Element('bar'));
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter        = $inputFilterFactory->createInputFilter([
            'foo'    => [
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
            'bar'    => [
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
            'foobar' => [
                'type' => InputFilter::class,
                'foo'  => [
                    'name'       => 'foo',
                    'required'   => true,
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
                'bar'  => [
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
        $this->form->setInputFilter($inputFilter);
    }

    public function testInputFilterPresentByDefault(): void
    {
        self::assertNotNull($this->form->getInputFilter());
    }

    public function testCanComposeAnInputFilter(): void
    {
        $filter = new InputFilter();
        $this->form->setInputFilter($filter);
        self::assertSame($filter, $this->form->getInputFilter());
    }

    public function testShouldThrowExceptionWhenGetInvalidElement(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->form->get('doesnt_exist');
    }

    public function testDefaultNonRequiredInputFilterIsSet(): void
    {
        $this->form->add(new Element('foo'));
        $inputFilter = $this->form->getInputFilter();
        $fooInput    = $inputFilter->get('foo');
        self::assertFalse($fooInput->isRequired());
    }

    public function testInputProviderInterfaceAddsInputFilters(): void
    {
        $form = new TestAsset\InputFilterProvider();

        $inputFilter = $form->getInputFilter();
        $fooInput    = $inputFilter->get('foo');
        self::assertTrue($fooInput->isRequired());
    }

    public function testInputProviderInterfaceAddsInputFiltersOnlyForSelf(): void
    {
        $form = new TestAsset\InputFilterProviderWithFieldset();

        $inputFilter         = $form->getInputFilter();
        $fieldsetInputFilter = $inputFilter->get('basic_fieldset');
        self::assertFalse($fieldsetInputFilter->has('foo'));
    }

    public function testCallingIsValidRaisesExceptionIfNoDataSet(): void
    {
        $this->expectException(DomainException::class);
        $this->form->isValid();
    }

    public function testHasValidatedFlag(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new TestAsset\NewProductForm();

        self::assertFalse($form->hasValidated());

        $form->setData([]);
        $form->isValid();

        self::assertTrue($form->hasValidated());
    }

    public function testValidatesEntireDataSetByDefault(): void
    {
        $this->populateForm();
        $invalidSet = [
            'foo'    => 'a',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'a',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($invalidSet);
        self::assertFalse($this->form->isValid());

        $validSet = [
            'foo'    => 'abcde',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($validSet);
        self::assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForcesPartialValidation(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->populateForm();
        $invalidSet = [
            'foo' => 'a',
        ];
        $this->form->setValidationGroup(['foo']);
        $this->form->setData($invalidSet);
        self::assertFalse($this->form->isValid());

        $validSet = [
            'foo' => 'abcde',
        ];
        $this->form->setData($validSet);
        self::assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForNestedFieldsetsForcesPartialValidation(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new TestAsset\NewProductForm();
        $form->setData([
            'product' => [
                'name' => 'Chair',
            ],
        ]);

        self::assertFalse($form->isValid());

        $form->setValidationGroup([
            'product' => [
                'name',
            ],
        ]);

        self::assertTrue($form->isValid());
    }

    public function testSettingValidateAllFlagAfterPartialValidationForcesFullValidation(): void
    {
        $this->populateForm();
        $this->form->setValidationGroup(['foo']);

        $validSet = [
            'foo' => 'abcde',
        ];
        $this->form->setData($validSet);
        $this->form->setValidateAll();
        self::assertFalse($this->form->isValid());
        $messages = $this->form->getMessages();
        self::assertArrayHasKey('foobar', $messages, var_export($messages, true));
    }

    public function testCallingGetDataPriorToValidationRaisesException(): void
    {
        $this->expectException(DomainException::class);
        $this->form->getData();
    }

    public function testAttemptingToValidateWithNoInputFilterAttachedRaisesException(): void
    {
        $this->expectException(DomainException::class);
        $this->form->isValid();
    }

    public function testCanRetrieveDataWithoutErrorsFollowingValidation(): void
    {
        $this->populateForm();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData();
        self::assertIsArray($data);
    }

    /**
     * @group Laminas-336
     */
    public function testCanAddFileEnctypeAttribute(): void
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions([])
            ->setLabel('File');
        $this->form->add($file);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        self::assertNotEmpty($enctype);
        self::assertEquals($enctype, 'multipart/form-data');
    }

    /**
     * @group Laminas-336
     */
    public function testCanAddFileEnctypeFromCollectionAttribute(): void
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions([])
            ->setLabel('File');

        $fileCollection = new Element\Collection('collection');
        $fileCollection->setOptions([
            'count'          => 2,
            'allow_add'      => false,
            'allow_remove'   => false,
            'target_element' => $file,
        ]);
        $this->form->add($fileCollection);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        self::assertNotEmpty($enctype);
        self::assertEquals($enctype, 'multipart/form-data');
    }

    public function testCallingGetDataReturnsNormalizedDataByDefault(): void
    {
        $this->populateForm();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();

        $expected = [
            'foo'    => 'abcde',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        self::assertEquals($expected, $data);
    }

    public function testAllowsReturningRawValuesViaGetData(): void
    {
        $this->populateForm();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_RAW);
        self::assertEquals($validSet, $data);
    }

    public function testGetDataReturnsBoundModel(): void
    {
        $model = new stdClass();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->populateForm();
        $this->form->setData([]);
        $this->form->bind($model);
        $this->form->isValid();
        $data = $this->form->getData();
        self::assertSame($model, $data);
    }

    public function testGetDataCanReturnValuesAsArrayWhenModelIsBound(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        self::assertEquals($validSet, $data);
    }

    public function testValuesBoundToModelAreNormalizedByDefault(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        self::assertTrue(isset($model->foo));
        self::assertEquals($validSet['foo'], $model->foo);
        self::assertTrue(isset($model->bar));
        self::assertEquals('always valid', $model->bar);
        self::assertTrue(isset($model->foobar));
        self::assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testCanBindRawValuesToModel(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model, Form::VALUES_RAW);
        $this->form->setData($validSet);
        $this->form->isValid();

        self::assertTrue(isset($model->foo));
        self::assertEquals($validSet['foo'], $model->foo);
        self::assertTrue(isset($model->bar));
        self::assertEquals(' ALWAYS valid ', $model->bar);
        self::assertTrue(isset($model->foobar));
        self::assertEquals([
            'foo' => 'abcde',
            'bar' => ' always VALID',
        ], $model->foobar);
    }

    public function testGetDataReturnsSubsetOfDataWhenValidationGroupSet(): void
    {
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setValidationGroup(['foo']);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();
        self::assertIsArray($data);
        self::assertCount(1, $data);
        self::assertArrayHasKey('foo', $data);
        self::assertEquals('abcde', $data['foo']);
    }

    public function testSettingValidationGroupBindsOnlyThoseValuesToModel(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->setValidationGroup(['foo']);
        $this->form->isValid();

        self::assertTrue(isset($model->foo));
        self::assertEquals('abcde', $model->foo);
        self::assertFalse(isset($model->bar));
        self::assertFalse(isset($model->foobar));
    }

    public function testFormWithCollectionAndValidationGroupBindValuesToModel(): void
    {
        $model = new stdClass();
        $data  = [
            'foo'        => 'abcde',
            'categories' => [
                [
                    'name' => 'category',
                ],
            ],
        ];
        $this->populateForm();
        $this->form->add([
            'type'    => Element\Collection::class,
            'name'    => 'categories',
            'options' => [
                'count'          => 0,
                'target_element' => [
                    'type' => TestAsset\CategoryFieldset::class,
                ],
            ],
        ]);
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($data);
        $this->form->setValidationGroup([
            'foo',
            'categories' => [
                'name',
            ],
        ]);
        $this->form->isValid();

        self::assertTrue(isset($model->foo));
        self::assertEquals('abcde', $model->foo);
        self::assertTrue(isset($model->categories));
        self::assertIsArray($model->categories);
        self::assertInstanceOf(Category::class, $model->categories[0]);
        self::assertEquals('category', $model->categories[0]->getName());
        self::assertFalse(isset($model->foobar));
    }

    public function testSettingValidationGroupWithoutCollectionBindsOnlyThoseValuesToModel(): void
    {
        $model                 = new stdClass();
        $dataWithoutCollection = [
            'foo' => 'abcde',
        ];
        $this->populateForm();
        $this->form->add([
            'type'    => Element\Collection::class,
            'name'    => 'categories',
            'options' => [
                'count'          => 0,
                'target_element' => [
                    'type' => TestAsset\CategoryFieldset::class,
                ],
            ],
        ]);
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($dataWithoutCollection);
        $this->form->setValidationGroup(['foo']);
        $this->form->isValid();

        self::assertTrue(isset($model->foo));
        self::assertEquals('abcde', $model->foo);
        self::assertFalse(isset($model->categories));
        self::assertFalse(isset($model->foobar));
    }

    public function testCanBindModelsToArraySerializableObjects(): void
    {
        $model    = new TestAsset\Model();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ArraySerializableHydrator());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        self::assertEquals('abcde', $model->foo);
        self::assertEquals('always valid', $model->bar);
        self::assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testSetsInputFilterToFilterFromBoundModelIfModelImplementsInputLocatorAware(): void
    {
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter(new InputFilter());
        $this->populateForm();
        $this->form->bind($model);
        self::assertSame($model->getInputFilter(), $this->form->getInputFilter());
    }

    public function testSettingDataShouldSetElementValues(): void
    {
        $this->populateForm();
        $data = [
            'foo'    => 'abcde',
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($data);

        $fieldset = $this->form->get('foobar');
        self::assertInstanceOf(Fieldset::class, $fieldset);
        foreach (['foo', 'bar'] as $name) {
            $element = $this->form->get($name);
            self::assertEquals($data[$name], $element->getValue());

            $element = $fieldset->get($name);
            self::assertEquals($data[$name], $element->getValue());
        }
    }

    public function testElementValuesArePopulatedFollowingBind(): void
    {
        $this->populateForm();
        $object      = new stdClass();
        $object->foo = 'foobar';
        $object->bar = 'barbaz';
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($object);

        $foo = $this->form->get('foo');
        self::assertEquals('foobar', $foo->getValue());
        $bar = $this->form->get('bar');
        self::assertEquals('barbaz', $bar->getValue());
    }

    public function testUsesBoundObjectAsDataSourceWhenNoDataSet(): void
    {
        $this->populateForm();
        $object         = new stdClass();
        $object->foo    = 'foos';
        $object->bar    = 'bar';
        $object->foobar = [
            'foo' => 'foos',
            'bar' => 'bar',
        ];
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($object);

        self::assertTrue($this->form->isValid());
    }

    public function testUsesBoundObjectHydratorToPopulateForm(): void
    {
        $this->populateForm();
        $object = new TestAsset\HydratorAwareModel();
        $object->setFoo('fooValue');
        $object->setBar('barValue');

        $this->form->bind($object);
        $foo = $this->form->get('foo');
        self::assertEquals('fooValue', $foo->getValue());
        $bar = $this->form->get('bar');
        self::assertEquals('barValue', $bar->getValue());
    }

    public function testBindOnValidateIsTrueByDefault(): void
    {
        self::assertTrue($this->form->bindOnValidate());
    }

    public function testCanDisableBindOnValidateFunctionality(): void
    {
        $this->form->setBindOnValidate(Form::BIND_MANUAL);
        self::assertFalse($this->form->bindOnValidate());
    }

    public function testCallingBindValuesWhenBindOnValidateIsDisabledPopulatesBoundObject(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->setBindOnValidate(Form::BIND_MANUAL);
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        self::assertFalse(isset($model->foo));
        self::assertFalse(isset($model->bar));
        self::assertFalse(isset($model->foobar));

        $this->form->bindValues();

        self::assertTrue(isset($model->foo));
        self::assertEquals($validSet['foo'], $model->foo);
        self::assertTrue(isset($model->bar));
        self::assertEquals('always valid', $model->bar);
        self::assertTrue(isset($model->foobar));
        self::assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testBindValuesWithWrappingPopulatesBoundObject(): void
    {
        $model    = new stdClass();
        $validSet = [
            'foo'    => 'abcde',
            'bar'    => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->setName('formName');
        $this->form->setWrapElements(true);
        $this->form->prepare();
        $this->form->bind($model);
        $this->form->setData($validSet);

        self::assertFalse(isset($model->foo));
        self::assertFalse(isset($model->bar));
        self::assertFalse(isset($model->foobar));

        $this->form->isValid();

        self::assertEquals($validSet['foo'], $model->foo);
        self::assertEquals('always valid', $model->bar);
        self::assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testFormBaseFieldsetBindValuesWithoutInputs(): void
    {
        $baseFieldset = new Fieldset('base_fieldset');
        $baseFieldset->setUseAsBaseFieldset(true);

        $form = new Form();
        $form->add($baseFieldset);
        $form->setHydrator(new ObjectPropertyHydrator());

        $model = new stdClass();
        $form->bind($model);

        $data = [
            'submit' => 'save',
        ];
        $form->setData($data);

        $form->isValid(); // Calls ->bindValues after validation (line: 817)

        self::assertFalse(isset($model->submit));
    }

    public function testHasFactoryComposedByDefault(): void
    {
        $factory = $this->form->getFormFactory();
        self::assertInstanceOf(Factory::class, $factory);
    }

    public function testCanComposeFactory(): void
    {
        $factory = new Factory();
        $this->form->setFormFactory($factory);
        self::assertSame($factory, $this->form->getFormFactory());
    }

    public function testCanAddElementsUsingSpecs(): void
    {
        $this->form->add([
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ],
        ]);
        self::assertTrue($this->form->has('foo'));
        $element = $this->form->get('foo');
        self::assertInstanceOf(ElementInterface::class, $element);
        self::assertEquals('foo', $element->getName());
        self::assertEquals('text', $element->getAttribute('type'));
        self::assertEquals('foo-class', $element->getAttribute('class'));
        self::assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanAddFieldsetsUsingSpecs(): void
    {
        $this->form->add([
            'type'       => Fieldset::class,
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ],
        ]);
        self::assertTrue($this->form->has('foo'));
        $fieldset = $this->form->get('foo');
        self::assertInstanceOf(FieldsetInterface::class, $fieldset);
        self::assertEquals('foo', $fieldset->getName());
        self::assertEquals('fieldset', $fieldset->getAttribute('type'));
        self::assertEquals('foo-class', $fieldset->getAttribute('class'));
        self::assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
    }

    public function testFormAsFieldsetWillBindValuesToObject(): void
    {
        $parentForm        = new Form('parent');
        $parentFormObject  = new ArrayObject(['parentId' => null]);
        $parentFormElement = new Element('parentId');
        $parentForm->setObject($parentFormObject);
        $parentForm->add($parentFormElement);

        $childForm        = new Form('child');
        $childFormObject  = new ArrayObject(['childId' => null]);
        $childFormElement = new Element('childId');
        $childForm->setObject($childFormObject);
        $childForm->add($childFormElement);

        $parentForm->add($childForm);

        $data = [
            'parentId' => 'mpinkston was here',
            'child'    => [
                'childId' => 'testing 123',
            ],
        ];

        $parentForm->setData($data);
        self::assertTrue($parentForm->isValid());
        self::assertEquals($data['parentId'], $parentFormObject['parentId']);
        self::assertEquals($data['child']['childId'], $childFormObject['childId']);
    }

    public function testWillUseInputSpecificationFromElementInInputFilterIfNoMatchingInputFound(): void
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        self::assertSame($filter, $test);
        self::assertTrue($filter->has('foo'));
        $input   = $filter->get('foo');
        $filters = $input->getFilterChain();
        self::assertCount(1, $filters);
        $validators = $input->getValidatorChain();
        self::assertCount(2, $validators);
        self::assertTrue($input->isRequired());
        self::assertEquals('foo', $input->getName());
    }

    public function testWillUseInputFilterSpecificationFromFieldsetInInputFilterIfNoMatchingInputFilterFound(): void
    {
        $fieldset = new TestAsset\FieldsetWithInputFilter('set');
        $filter   = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        self::assertSame($filter, $test);
        self::assertTrue($filter->has('set'));
        $input = $filter->get('set');
        self::assertInstanceOf(InputFilterInterface::class, $input);
        self::assertCount(2, $input);
        self::assertTrue($input->has('foo'));
        self::assertTrue($input->has('bar'));
    }

    public function testWillPopulateSubInputFilterFromInputSpecificationsOnFieldsetElements(): void
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        self::assertSame($filter, $test);
        $test = $filter->get('set');
        self::assertSame($fieldsetFilter, $test);

        self::assertCount(1, $fieldsetFilter);
        self::assertTrue($fieldsetFilter->has('foo'));

        $input = $fieldsetFilter->get('foo');
        self::assertInstanceOf(InputInterface::class, $input);
        $filters = $input->getFilterChain();
        self::assertCount(1, $filters);
        $validators = $input->getValidatorChain();
        self::assertCount(2, $validators);
        self::assertTrue($input->isRequired());
        self::assertEquals('foo', $input->getName());
    }

    public function testWillUseFormInputFilterOverrideOverInputSpecificationFromElement(): void
    {
        $element       = new TestAsset\ElementWithFilter('foo');
        $filter        = new InputFilter();
        $filterFactory = new InputFilterFactory();
        $filter        = $filterFactory->createInputFilter([
            'foo' => [
                'name'     => 'foo',
                'required' => false,
            ],
        ]);
        $this->form->setPreferFormInputFilter(true);
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        self::assertSame($filter, $test);
        self::assertTrue($filter->has('foo'));
        $input   = $filter->get('foo');
        $filters = $input->getFilterChain();
        self::assertCount(0, $filters);
        $validators = $input->getValidatorChain();
        self::assertCount(0, $validators);
        self::assertFalse($input->isRequired());
        self::assertEquals('foo', $input->getName());
    }

    public function testDisablingUseInputFilterDefaultsFlagDisablesInputFilterScanning(): void
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $this->form->setUseInputFilterDefaults(false);
        $test = $this->form->getInputFilter();
        self::assertSame($filter, $test);
        self::assertSame($fieldsetFilter, $test->get('set'));
        self::assertCount(0, $fieldsetFilter);
    }

    public function testCallingPrepareEnsuresInputFilterRetrievesDefaults(): void
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);
        $this->form->prepare();

        self::assertTrue($filter->has('foo'));
        $input   = $filter->get('foo');
        $filters = $input->getFilterChain();
        self::assertCount(1, $filters);
        $validators = $input->getValidatorChain();
        self::assertCount(2, $validators);
        self::assertTrue($input->isRequired());
        self::assertEquals('foo', $input->getName());

        // Issue #2586 Ensure default filters aren't added twice
        $filter = $this->form->getInputFilter();

        self::assertTrue($filter->has('foo'));
        $input   = $filter->get('foo');
        $filters = $input->getFilterChain();
        self::assertCount(1, $filters);
        $validators = $input->getValidatorChain();
        self::assertCount(2, $validators);
    }

    public function testCanProperlyPrepareNestedFieldsets(): void
    {
        $this->form->add([
            'name'       => 'foo',
            'attributes' => [
                'type' => 'text',
            ],
        ]);

        $this->form->add([
            'type' => TestAsset\BasicFieldset::class,
        ]);

        $this->form->prepare();

        self::assertEquals('foo', $this->form->get('foo')->getName());

        $basicFieldset = $this->form->get('basic_fieldset');
        self::assertInstanceOf(Fieldset::class, $basicFieldset);
        self::assertEquals('basic_fieldset[field]', $basicFieldset->get('field')->getName());

        $nestedFieldset = $basicFieldset->get('nested_fieldset');
        self::assertInstanceOf(Fieldset::class, $nestedFieldset);
        self::assertEquals(
            'basic_fieldset[nested_fieldset][anotherField]',
            $nestedFieldset->get('anotherField')->getName()
        );
    }

    public function testCanCorrectlyExtractDataFromComposedEntities(): void
    {
        $address = $this->getComposedEntity();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($address);
        $form->setBindOnValidate(Form::BIND_MANUAL);

        self::assertEquals(true, $form->isValid());
        self::assertEquals($address, $form->getData());
    }

    public function testCanCorrectlyPopulateDataToComposedEntities(): void
    {
        $address      = $this->getComposedEntity();
        $emptyAddress = new TestAsset\Entity\Address();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($emptyAddress);

        $data = [
            'address' => [
                'street' => '1 Rue des Champs Elysées',
                'city'   => [
                    'name'    => 'Paris',
                    'zipCode' => '75008',
                    'country' => [
                        'name'      => 'France',
                        'continent' => 'Europe',
                    ],
                ],
            ],
        ];

        $form->setData($data);

        self::assertEquals(true, $form->isValid());
        self::assertEquals(
            $address,
            $emptyAddress,
            var_export($address, true) . "\n\n" . var_export($emptyAddress, true)
        );
    }

    public function testCanCorrectlyExtractDataFromOneToManyRelationship(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $product = $this->getOneToManyEntity();

        $form = new TestAsset\NewProductForm();
        $form->bind($product);
        $form->setBindOnValidate(Form::BIND_MANUAL);

        self::assertEquals(true, $form->isValid());
        self::assertEquals($product, $form->getData());
    }

    public function testCanCorrectlyPopulateDataToOneToManyEntites(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('The Intl extension is not loaded');
        }
        $product      = $this->getOneToManyEntity();
        $emptyProduct = new TestAsset\Entity\Product();

        $form = new TestAsset\NewProductForm();
        $form->bind($emptyProduct);

        $data = [
            'product' => [
                'name'       => 'Chair',
                'price'      => 10,
                'categories' => [
                    [
                        'name' => 'Office',
                    ],
                    [
                        'name' => 'Armchair',
                    ],
                ],
            ],
        ];

        $form->setData($data);

        self::assertEquals(true, $form->isValid());
        self::assertEquals(
            $product,
            $emptyProduct,
            var_export($product, true) . "\n\n" . var_export($emptyProduct, true)
        );
    }

    public function testCanCorrectlyPopulateOrphanedEntities(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('The Intl extension is not loaded');
        }

        $form = new TestAsset\OrphansForm();

        $data = [
            'test' => [
                [
                    'name' => 'Foo',
                ],
                [
                    'name' => 'Bar',
                ],
            ],
        ];

        $form->setData($data);
        $valid = $form->isValid();
        self::assertEquals(true, $valid);

        $formCollections = $form->getFieldsets();
        $formCollection  = $formCollections['test'];

        $fieldsets = $formCollection->getFieldsets();

        $fieldsetFoo = $fieldsets[0];
        $fieldsetBar = $fieldsets[1];

        $objectFoo = $fieldsetFoo->getObject();
        self::assertInstanceOf(
            TestAsset\Entity\Orphan::class,
            $objectFoo,
            'FormCollection with orphans does not bind objects from fieldsets'
        );

        $objectBar = $fieldsetBar->getObject();
        self::assertInstanceOf(
            TestAsset\Entity\Orphan::class,
            $objectBar,
            'FormCollection with orphans does not bind objects from fieldsets'
        );

        self::assertSame(
            'Foo',
            $objectFoo->name,
            'Object is not populated if it is an orphan in a fieldset inside a formCollection'
        );

        self::assertSame(
            'Bar',
            $objectBar->name,
            'Object is not populated if it is an orphan in a fieldset inside a formCollection'
        );
    }

    public function testAssertElementsNamesAreNotWrappedAroundFormNameByDefault(): void
    {
        $form = new TestAsset\FormCollection();
        $form->prepare();

        $colors = $form->get('colors');
        self::assertInstanceOf(Fieldset::class, $colors);
        self::assertEquals('colors[0]', $colors->get('0')->getName());
        $fieldsets = $form->get('fieldsets');
        self::assertInstanceOf(Fieldset::class, $fieldsets);
        $zeroFieldset = $fieldsets->get('0');
        self::assertInstanceOf(Fieldset::class, $zeroFieldset);
        self::assertEquals('fieldsets[0][field]', $zeroFieldset->get('field')->getName());
    }

    public function testAssertElementsNamesCanBeWrappedAroundFormName(): void
    {
        $form = new TestAsset\FormCollection();
        $form->setWrapElements(true);
        $form->setName('foo');
        $form->prepare();

        $colors = $form->get('colors');
        self::assertInstanceOf(Fieldset::class, $colors);
        self::assertEquals('foo[colors][0]', $colors->get('0')->getName());
        $fieldsets = $form->get('fieldsets');
        self::assertInstanceOf(Fieldset::class, $fieldsets);
        $zeroFieldset = $fieldsets->get('0');
        self::assertInstanceOf(Fieldset::class, $zeroFieldset);
        self::assertEquals('foo[fieldsets][0][field]', $zeroFieldset->get('field')->getName());
    }

    public function testUnsetValuesNotBound(): void
    {
        $model    = new stdClass();
        $validSet = [
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new ObjectPropertyHydrator());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();
        self::assertInstanceOf(stdClass::class, $data);
        self::assertFalse(isset($data->foo));
        self::assertTrue(isset($data->bar));
    }

    public function testRemoveCollectionFromValidationGroupWhenZeroCountAndNoData(): void
    {
        $dataWithoutCollection = [
            'foo' => 'bar',
        ];
        $this->populateForm();
        $this->form->add([
            'type'    => Element\Collection::class,
            'name'    => 'categories',
            'options' => [
                'count'          => 0,
                'target_element' => [
                    'type' => TestAsset\CategoryFieldset::class,
                ],
            ],
        ]);
        $this->form->setValidationGroup([
            'foo',
            'categories' => [
                'name',
            ],
        ]);
        $this->form->setData($dataWithoutCollection);
        self::assertTrue($this->form->isValid());
    }

    public function testFieldsetValidationGroupStillPreparedWhenEmptyData(): void
    {
        $emptyData = [];

        $this->populateForm();
        $foobarFieldset = $this->form->get('foobar');
        self::assertInstanceOf(Fieldset::class, $foobarFieldset);
        $foobarFieldset->add([
            'type'    => Element\Collection::class,
            'name'    => 'categories',
            'options' => [
                'count'          => 0,
                'target_element' => [
                    'type' => TestAsset\CategoryFieldset::class,
                ],
            ],
        ]);

        $this->form->setValidationGroup([
            'foobar' => [
                'categories' => [
                    'name',
                ],
            ],
        ]);

        $this->form->setData($emptyData);
        self::assertFalse($this->form->isValid());
    }

    public function testApplyObjectInputFilterToBaseFieldsetAndApplyValidationGroup(): void
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setValidationGroup([
            'foobar' => [
                'foo',
            ],
        ]);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter        = $inputFilterFactory->createInputFilter([
            'foo' => [
                'name'     => 'foo',
                'required' => true,
            ],
        ]);
        $model              = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);
        $this->form->bind($model);

        $this->form->setData([]);
        self::assertFalse($this->form->isValid());

        $validSet = [
            'foobar' => [
                'foo' => 'abcde',
            ],
        ];
        $this->form->setData($validSet);
        self::assertTrue($this->form->isValid());
    }

    public function testDoNotApplyEmptyInputFiltersToSubFieldsetOfCollectionElementsWithCollectionInputFilters(): void
    {
        $collectionFieldset = new Fieldset('item');
        $collectionFieldset->add(new Element('foo'));

        $collection = new Element\Collection('items');
        $collection->setCount(3);
        $collection->setTargetElement($collectionFieldset);
        $this->form->add($collection);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter        = $inputFilterFactory->createInputFilter([
            'items' => [
                'type'         => CollectionInputFilter::class,
                'input_filter' => new InputFilter(),
            ],
        ]);

        $this->form->setInputFilter($inputFilter);

        $itemsInputFilter = $this->form->getInputFilter()->get('items');
        self::assertInstanceOf(CollectionInputFilter::class, $itemsInputFilter);
        self::assertCount(1, $itemsInputFilter->getInputFilter()->getInputs());
    }

    public function testFormValidationCanHandleNonConsecutiveKeysOfCollectionInData(): void
    {
        $dataWithCollection = [
            'foo'        => 'bar',
            'categories' => [
                0 => ['name' => 'cat1'],
                1 => ['name' => 'cat2'],
                3 => ['name' => 'cat3'],
            ],
        ];
        $this->populateForm();
        $this->form->add([
            'type'    => Element\Collection::class,
            'name'    => 'categories',
            'options' => [
                'count'          => 1,
                'allow_add'      => true,
                'target_element' => [
                    'type' => TestAsset\CategoryFieldset::class,
                ],
            ],
        ]);
        $this->form->setValidationGroup([
            'foo',
            'categories' => [
                'name',
            ],
        ]);
        $this->form->setData($dataWithCollection);
        self::assertTrue($this->form->isValid());
    }

    public function testAddNonBaseFieldsetObjectInputFilterToFormInputFilter(): void
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(false);
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter        = $inputFilterFactory->createInputFilter([
            'foo' => [
                'name'     => 'foo',
                'required' => true,
            ],
        ]);
        $model              = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);

        $this->form->bind($model);

        self::assertInstanceOf(InputFilterInterface::class, $this->form->getInputFilter()->get('foobar'));
    }

    public function testExtractDataHydratorStrategy(): void
    {
        $this->populateHydratorStrategyForm();

        $hydrator = new ObjectPropertyHydrator();
        $hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());
        $this->form->setHydrator($hydrator);

        $model = new TestAsset\HydratorStrategyEntityA();
        $this->form->bind($model);

        $validSet = [
            'entities' => [
                111,
                333,
            ],
        ];

        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        self::assertEquals($validSet, $data);

        $entities = $model->getEntities();
        self::assertCount(2, $entities);

        self::assertEquals(111, $entities[0]->getField1());
        self::assertEquals(333, $entities[1]->getField1());

        self::assertEquals('AAA', $entities[0]->getField2());
        self::assertEquals('CCC', $entities[1]->getField2());
    }

    public function testSetDataWithNullValues(): void
    {
        $this->populateForm();

        $set = [
            'foo'    => null,
            'bar'    => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($set);
        self::assertTrue($this->form->isValid());
    }

    public function testHydratorAppliedToBaseFieldset(): void
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setHydrator(new ArraySerializableHydrator());

        $foobarFieldset = $this->form->get('foobar');
        self::assertInstanceOf(Fieldset::class, $foobarFieldset);
        $baseHydrator = $foobarFieldset->getHydrator();
        self::assertInstanceOf(ArraySerializableHydrator::class, $baseHydrator);
    }

    public function testBindWithWrongFlagRaisesException(): void
    {
        $model = new stdClass();
        $this->expectException(InvalidArgumentException::class);
        $this->form->bind($model, Form::VALUES_AS_ARRAY);
    }

    public function testSetBindOnValidateWrongFlagRaisesException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->form->setBindOnValidate(Form::VALUES_AS_ARRAY);
    }

    public function testSetDataIsTraversable(): void
    {
        $this->form->setData(new ArrayObject(['foo' => 'bar']));
        self::assertTrue($this->form->isValid());
    }

    public function testResetPasswordValueIfFormIsNotValid(): void
    {
        $this->form->add([
            'type' => Element\Password::class,
            'name' => 'password',
        ]);

        $this->form->add([
            'type' => Element\Email::class,
            'name' => 'email',
        ]);

        $this->form->setData([
            'password' => 'azerty',
            'email'    => 'wrongEmail',
        ]);

        self::assertFalse($this->form->isValid());
        $this->form->prepare();

        self::assertEquals('', $this->form->get('password')->getValue());
    }

    public function testCorrectlyHydrateBaseFieldsetWhenHydratorThatDoesNotIgnoreInvalidDataIsUsed(): void
    {
        $fieldset = new Fieldset('example');
        $fieldset->add([
            'name' => 'foo',
        ]);

        // Add a hydrator that ignores if values does not exist in the
        $fieldset->setObject(new TestAsset\Entity\SimplePublicProperty());
        $fieldset->setHydrator(new ObjectPropertyHydrator());

        $this->form->add($fieldset);
        $this->form->setBaseFieldset($fieldset);
        $this->form->setHydrator(new ObjectPropertyHydrator());

        // Add some inputs that do not belong to the base fieldset
        $this->form->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
        ]);

        $object = new TestAsset\Entity\SimplePublicProperty();
        $this->form->bind($object);

        $this->form->setData([
            'submit'  => 'Confirm',
            'example' => [
                'foo' => 'value example',
            ],
        ]);

        self::assertTrue($this->form->isValid());

        // Make sure the object was not hydrated at the "form level"
        self::assertFalse(isset($object->submit));
    }

    public function testPrepareBindDataAllowsFilterToConvertStringToArray(): void
    {
        $data = [
            'foo' => '1,2',
        ];

        $filteredData = [
            'foo' => [1, 2],
        ];

        $element  = new TestAsset\ElementWithStringToArrayFilter('foo');
        $hydrator = $this->createMock(ArraySerializableHydrator::class);
        $hydrator->expects($this->any())->method('hydrate')->with($filteredData, $this->anything());

        $this->form->add($element);
        $this->form->setHydrator($hydrator);
        $this->form->setObject(new stdClass());
        $this->form->setData($data);
        $this->form->bindValues($data);

        $this->addToAssertionCount(1);
    }

    public function testGetValidationGroup(): void
    {
        $group = ['foo'];
        $this->form->setValidationGroup($group);
        self::assertEquals($group, $this->form->getValidationGroup());
    }

    public function testGetValidationGroupReturnsNullWhenNoneSet(): void
    {
        self::assertNull($this->form->getValidationGroup());
    }

    public function testPreserveEntitiesBoundToCollectionAfterValidation(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->form->setInputFilter(new InputFilter());
        $fieldset = new TestAsset\ProductCategoriesFieldset();
        $fieldset->setUseAsBaseFieldset(true);

        $product = new TestAsset\Entity\Product();
        $product->setName('Foobar');
        $product->setPrice(100);

        $c1 = new Category();
        $c1->setId(1);
        $c1->setName('First Category');

        $c2 = new Category();
        $c2->setId(2);
        $c2->setName('Second Category');

        $product->setCategories([$c1, $c2]);

        $this->form->add($fieldset);
        $this->form->bind($product);

        $data = [
            'product' => [
                'name'       => 'Barbar',
                'price'      => 200,
                'categories' => [
                    ['name' => 'Something else'],
                    ['name' => 'Totally different'],
                ],
            ],
        ];

        $hash1 = spl_object_hash($this->form->getObject()->getCategory(0));
        $this->form->setData($data);
        $this->form->isValid();
        $hash2 = spl_object_hash($this->form->getObject()->getCategory(0));

        // Returned object has to be the same as when binding or properties
        // will be lost. (For example entity IDs.)
        self::assertEquals($hash1, $hash2);
    }

    public function testAddRemove(): void
    {
        $form = clone $this->form;
        self::assertEquals($form, $this->form);

        $file = new Element\File('file_resource');
        $this->form->add($file);
        self::assertTrue($this->form->has('file_resource'));
        self::assertNotEquals($form, $this->form);

        $form->add($file)->remove('file_resource');
        $this->form->remove('file_resource');
        self::assertEquals($form, $this->form);
    }

    public function testNestedFormElementNameWrapping(): void
    {
        //build form
        $rootForm = new Form();
        $leafForm = new Form('form');
        $element  = new Element('element');
        $leafForm->setWrapElements(true);
        $leafForm->add($element);
        $rootForm->add($leafForm);

        //prepare for view
        $rootForm->prepare();
        self::assertEquals('form[element]', $element->getName());
    }

    /**
     * @group issue-4996
     */
    public function testCanOverrideDefaultInputSettings(): void
    {
        $myFieldset = new TestAsset\MyFieldset();
        $myFieldset->setUseAsBaseFieldset(true);
        $form = new Form();
        $form->add($myFieldset);

        $inputFilter = $form->getInputFilter()->get('my-fieldset');
        self::assertFalse($inputFilter->get('email')->isRequired());
    }

    /**
     * @group issue-5007
     */
    public function testComplexFormInputFilterMergesIntoExisting(): void
    {
        $this->form->setPreferFormInputFilter(true);
        $this->form->add([
            'name'    => 'importance',
            'type'    => Element\Select::class,
            'options' => [
                'label'         => 'Importance',
                'empty_option'  => '',
                'value_options' => [
                    'normal'    => 'Normal',
                    'important' => 'Important',
                ],
            ],
        ]);

        $inputFilter = new BaseInputFilter();
        $factory     = new InputFilterFactory();
        $inputFilter->add($factory->createInput([
            'name'     => 'importance',
            'required' => false,
        ]));

        self::assertTrue($this->form->getInputFilter()->get('importance')->isRequired());
        self::assertFalse($inputFilter->get('importance')->isRequired());
        $this->form->getInputFilter();
        $this->form->setInputFilter($inputFilter);
        self::assertFalse($this->form->getInputFilter()->get('importance')->isRequired());
    }

    /**
     * @group issue-5007
     */
    public function testInputFilterOrderOfPrecedence1(): void
    {
        $spec = [
            'name'         => 'test',
            'elements'     => [
                [
                    'spec' => [
                        'name'    => 'element',
                        'type'    => Element\Checkbox::class,
                        'options' => [
                            'use_hidden_element' => true,
                            'checked_value'      => '1',
                            'unchecked_value'    => '0',
                        ],
                    ],
                ],
            ],
            'input_filter' => [
                'element' => [
                    'required'   => false,
                    'filters'    => [
                        [
                            'name' => 'Boolean',
                        ],
                    ],
                    'validators' => [
                        [
                            'name'    => 'InArray',
                            'options' => [
                                'haystack' => [
                                    '0',
                                    '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $factory = new Factory();
        $form    = $factory->createForm($spec);
        self::assertInstanceOf(Form::class, $form);
        $form->setPreferFormInputFilter(true);
        self::assertFalse(
            $form->getInputFilter()->get('element')->isRequired()
        );
    }

    /**
     * @group issue-5015
     */
    public function testCanSetPreferFormInputFilterFlagViaSetOptions(): void
    {
        $flag = ! $this->form->getPreferFormInputFilter();
        $this->form->setOptions([
            'prefer_form_input_filter' => $flag,
        ]);
        self::assertSame($flag, $this->form->getPreferFormInputFilter());
    }

    /**
     * @group issue-5015
     */
    public function testFactoryCanSetPreferFormInputFilterFlag(): void
    {
        $factory = new Factory();
        foreach ([true, false] as $flag) {
            $form = $factory->createForm([
                'name'    => 'form',
                'options' => [
                    'prefer_form_input_filter' => $flag,
                ],
            ]);
            self::assertInstanceOf(Form::class, $form);
            self::assertSame($flag, $form->getPreferFormInputFilter());
        }
    }

    /**
     * @group issue-5028
     */
    public function testPreferFormInputFilterFlagIsEnabledByDefault(): void
    {
        self::assertTrue($this->form->getPreferFormInputFilter());
    }

    /**
     * @group issue-5050
     */
    public function testFileInputFilterNotOverwritten(): void
    {
        $form = new TestAsset\FileInputFilterProviderForm();

        $formInputFilter     = $form->getInputFilter();
        $fieldsetInputFilter = $formInputFilter->get('file_fieldset');
        $fileInput           = $fieldsetInputFilter->get('file_field');

        self::assertInstanceOf(FileInput::class, $fileInput);

        $chain = $fileInput->getFilterChain();
        self::assertCount(1, $chain, (string) print_r($chain, true));
    }

    public function testInputFilterNotAddedTwiceWhenUsingFieldsets(): void
    {
        $form = new Form();

        $fieldset = new TestAsset\FieldsetWithInputFilter('fieldset');
        $form->add($fieldset);
        $filters = $form->getInputFilter()->get('fieldset')->get('foo')->getFilterChain();
        self::assertEquals(1, $filters->count());
    }

    public function testFormWithNestedCollections(): void
    {
        $spec = [
            'name'         => 'test',
            'elements'     => [
                [
                    'spec' => [
                        'name'    => 'groups',
                        'type'    => Element\Collection::class,
                        'options' => [
                            'target_element' => [
                                'type'     => Fieldset::class,
                                'name'     => 'group',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'type' => Element\Text::class,
                                            'name' => 'group_class',
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'type'    => Element\Collection::class,
                                            'name'    => 'items',
                                            'options' => [
                                                'target_element' => [
                                                    'type'     => Fieldset::class,
                                                    'name'     => 'item',
                                                    'elements' => [
                                                        [
                                                            'spec' => [
                                                                'type' => Element\Text::class,
                                                                'name' => 'id',
                                                            ],
                                                        ],
                                                        [
                                                            'spec' => [
                                                                'type' => Element\Text::class,
                                                                'name' => 'type',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'input_filter' => [
                'type'   => InputFilter::class,
                'name'   => [
                    'filters'    => [
                        ['name' => 'StringTrim'],
                        ['name' => 'Null'],
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'max' => 255,
                            ],
                        ],
                    ],
                ],
                'groups' => [
                    'type'         => CollectionInputFilter::class,
                    'input_filter' => [
                        'type'        => InputFilter::class,
                        'group_class' => [
                            'required' => false,
                        ],
                        'items'       => [
                            'type'         => CollectionInputFilter::class,
                            'input_filter' => [
                                'id'   => [
                                    'required' => false,
                                ],
                                'type' => [
                                    'required' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $factory    = new Factory();
        $this->form = $factory->createForm($spec);

        $data = [
            'name'   => 'foo',
            'groups' => [
                [
                    'group_class' => 'bar',
                    'items'       => [
                        [
                            'id'   => 100,
                            'type' => 'item-1',
                        ],
                    ],
                ],
                [
                    'group_class' => 'bar',
                    'items'       => [
                        [
                            'id'   => 200,
                            'type' => 'item-2',
                        ],
                        [
                            'id'   => 300,
                            'type' => 'item-3',
                        ],
                        [
                            'id'   => 400,
                            'type' => 'item-4',
                        ],
                    ],
                ],
                [
                    'group_class' => 'biz',
                    'items'       => [],
                ],
            ],
        ];

        $this->form->setData($data);

        $isValid = $this->form->isValid();
        self::assertEquals($data, $this->form->getData());
    }

    public function testFormWithCollectionsAndNestedFieldsetsWithInputFilterProviderInterface(): void
    {
        $this->form->add([
            'type'    => Element\Collection::class,
            'name'    => 'nested_fieldset_with_input_filter_provider',
            'options' => [
                'label'          => 'InputFilterProviderFieldset',
                'count'          => 1,
                'target_element' => [
                    'type' => TestAsset\InputFilterProviderFieldset::class,
                ],
            ],
        ]);

        $nestedInputFilter = $this->form->getInputFilter()
            ->get('nested_fieldset_with_input_filter_provider');
        self::assertInstanceOf(CollectionInputFilter::class, $nestedInputFilter);
        self::assertInstanceOf(Input::class, $nestedInputFilter->getInputFilter()->get('foo'));
    }

    public function testFormElementValidatorsMergeIntoAppliedInputFilter(): void
    {
        $this->form->add([
            'name'    => 'importance',
            'type'    => Element\Select::class,
            'options' => [
                'label'         => 'Importance',
                'empty_option'  => '',
                'value_options' => [
                    'normal'    => 'Normal',
                    'important' => 'Important',
                ],
            ],
        ]);

        $inputFilter = new BaseInputFilter();
        $factory     = new InputFilterFactory();
        $inputFilter->add($factory->createInput([
            'name'     => 'importance',
            'required' => false,
        ]));

        $data = [
            'importance' => 'unimporant',
        ];

        $this->form->setInputFilter($inputFilter);
        $this->form->setData($data);
        self::assertFalse($this->form->isValid());

        $data = [];

        $this->form->setData($data);
        self::assertTrue($this->form->isValid());
    }

    /**
     * @dataProvider formWithSelectMultipleAndEmptyUnselectedValueDataProvider
     */
    public function testFormWithSelectMultipleAndEmptyUnselectedValue(
        bool $expectedIsValid,
        array $expectedFormData,
        array $data,
        string $unselectedValue,
        bool $useHiddenElement
    ): void {
        $this->form->add([
            'name'       => 'multipleSelect',
            'type'       => Element\Select::class,
            'attributes' => ['multiple' => 'multiple'],
            'options'    => [
                'label'              => 'Importance',
                'use_hidden_element' => $useHiddenElement,
                'unselected_value'   => $unselectedValue,
                'value_options'      => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                ],
            ],
        ]);

        $actualIsValid = $this->form->setData($data)->isValid();
        self::assertEquals($expectedIsValid, $actualIsValid);

        $formData = $this->form->getData();
        self::assertEquals($expectedFormData, $formData);
    }

    /**
     * @return array
     */
    public static function formWithSelectMultipleAndEmptyUnselectedValueDataProvider(): array
    {
        return [
            [
                true,
                ['multipleSelect' => ['foo']],
                ['multipleSelect' => ['foo']],
                '',
                true,
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => ''],
                '',
                true,
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => 'empty'],
                'empty',
                true,
            ],
            [
                false,
                ['multipleSelect' => ''],
                ['multipleSelect' => ''],
                'empty',
                true,
            ],
            [
                false,
                ['multipleSelect' => ''],
                ['multipleSelect' => ''],
                '',
                false,
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => 'foo'],
                'foo',
                true,
            ],
        ];
    }

    public function testCanSetUseInputFilterDefaultsViaArray(): void
    {
        $spec = [
            'name'    => 'test',
            'options' => [
                'use_input_filter_defaults' => false,
            ],
        ];

        $factory = new Factory();
        $form    = $factory->createForm($spec);
        self::assertInstanceOf(Form::class, $form);
        self::assertFalse($form->useInputFilterDefaults());
    }

    /**
     * Error test for https://github.com/zendframework/zf2/issues/6363 comment #1
     */
    public function testSetValidationGroupOnFormWithNestedCollectionsRaisesInvalidArgumentException(): void
    {
        $this->form = new TestAsset\NestedCollectionsForm();

        $data = [
            'testFieldset' => [
                'groups' => [
                    [
                        'name'  => 'first',
                        'items' => [
                            [
                                'itemId' => 1,
                            ],
                            [
                                'itemId' => 2,
                            ],
                        ],
                    ],
                    [
                        'name'  => 'second',
                        'items' => [
                            [
                                'itemId' => 3,
                            ],
                        ],
                    ],
                    [
                        'name'  => 'third',
                        'items' => [],
                    ],
                ],
            ],
        ];

        $this->form->setData($data);
        $this->form->isValid();

        self::assertEquals($data, $this->form->getData());
    }

    /**
     * Test for https://github.com/zendframework/zf2/issues/6363 comment #2
     */
    public function testSetValidationGroupOnFormWithNestedCollectionsPopulatesOnlyFirstNestedCollectionElement(): void
    {
        $this->form = new TestAsset\NestedCollectionsForm();

        $data = [
            'testFieldset' => [
                'groups' => [
                    [
                        'name'  => 'first',
                        'items' => [
                            [
                                'itemId' => 1,
                            ],
                            [
                                'itemId' => 2,
                            ],
                        ],
                    ],
                    [
                        'name'  => 'second',
                        'items' => [
                            [
                                'itemId' => 3,
                            ],
                            [
                                'itemId' => 4,
                            ],
                        ],
                    ],
                    [
                        'name'  => 'third',
                        'items' => [
                            [
                                'itemId' => 5,
                            ],
                            [
                                'itemId' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->form->setData($data);
        $this->form->isValid();

        self::assertEquals($data, $this->form->getData());
    }

    /**
     * Test for https://github.com/zendframework/zend-form/pull/24#issue-119023527
     */
    public function testGetInputFilterInjectsFormInputFilterFactoryInstanceObjectIsNull(): void
    {
        $inputFilterFactory = $this->form->getFormFactory()->getInputFilterFactory();
        $inputFilter        = $this->form->getInputFilter();
        self::assertInstanceOf(InputFilter::class, $inputFilter);
        self::assertSame($inputFilterFactory, $inputFilter->getFactory());
    }

    /**
     * Test for https://github.com/zendframework/zend-form/pull/24#issuecomment-159905491
     */
    public function testGetInputFilterInjectsFormInputFilterFactoryInstanceWhenObjectIsInputFilterAware(): void
    {
        $this->form->setBaseFieldset(new Fieldset());
        $this->form->setHydrator(new ClassMethodsHydrator());
        $this->form->bind(new TestAsset\Entity\Cat());
        $inputFilterFactory = $this->form->getFormFactory()->getInputFilterFactory();
        $inputFilter        = $this->form->getInputFilter();
        self::assertInstanceOf(InputFilter::class, $inputFilter);
        self::assertSame($inputFilterFactory, $inputFilter->getFactory());
    }

    public function testShouldHydrateEmptyCollection(): void
    {
        $fieldset = new Fieldset('example');
        $fieldset->add([
            'type'    => Element\Collection::class,
            'name'    => 'foo',
            'options' => [
                'label'          => 'InputFilterProviderFieldset',
                'count'          => 1,
                'target_element' => [
                    'type' => 'text',
                ],
            ],
        ]);

        $this->form->add($fieldset);
        $this->form->setBaseFieldset($fieldset);
        $this->form->setHydrator(new ObjectPropertyHydrator());

        $object      = new TestAsset\Entity\SimplePublicProperty();
        $object->foo = ['item 1', 'item 2'];

        $this->form->bind($object);

        $this->form->setData([
            'submit'  => 'Confirm',
            'example' => [
                //'foo' => [] // $_POST doesn't have this if collection is empty
            ],
        ]);

        self::assertTrue($this->form->isValid());
        self::assertEquals([], $this->form->getObject()->foo);
    }

    /**
     * Test for https://github.com/zendframework/zend-form/issues/83
     */
    public function testCanBindNestedCollectionAfterPrepare(): void
    {
        $collection = new Element\Collection('numbers');
        $collection->setOptions([
            'count'          => 2,
            'allow_add'      => false,
            'allow_remove'   => false,
            'target_element' => [
                'type' => TestAsset\PhoneFieldset::class,
            ],
        ]);

        $form   = new Form();
        $object = new ArrayObject();
        $phone1 = new TestAsset\Entity\Phone();
        $phone2 = new TestAsset\Entity\Phone();
        $phone1->setNumber('unmodified');
        $phone2->setNumber('unmodified');
        $collection->setObject([$phone1, $phone2]);

        $form->setObject($object);
        $form->add($collection);

        $value = [
            'numbers' => [
                [
                    'id'     => '1',
                    'number' => 'modified',
                ],
                [
                    'id'     => '2',
                    'number' => 'modified',
                ],
            ],
        ];

        $form->prepare();

        $form->bindValues($value);

        $fieldsets = $collection->getFieldsets();

        $fieldsetFoo = $fieldsets[0];
        $fieldsetBar = $fieldsets[1];

        self::assertEquals($value['numbers'][0]['number'], $fieldsetFoo->getObject()->getNumber());
        self::assertEquals($value['numbers'][1]['number'], $fieldsetBar->getObject()->getNumber());
    }

    public function testNullDataAreKeptNullToBoundObjects(): void
    {
        $object = new class {
            /** @var null|int */
            public $foo = 123;
        };

        $form = new Form();
        $form->add(new Element('foo'));
        $form->setHydrator(new ObjectPropertyHydrator());
        $form->bind($object);

        $form->setData(['foo' => null]);

        self::assertTrue($form->isValid());
        self::assertNull($object->foo);
    }

    public function testSetInputFilterByNameMethodShouldSetValidInputFilterForForm(): void
    {
        $inputFilterName    = uniqid('input_filter_');
        $inputFilter        = $this->createMock(InputFilterInterface::class);
        $inputFilterFactory = new InputFilterFactory();
        $inputFilterFactory->getInputFilterManager()->setService($inputFilterName, $inputFilter);

        $this->form->getFormFactory()->setInputFilterFactory($inputFilterFactory);

        $this->form->setInputFilterByName($inputFilterName);

        self::assertSame($inputFilter, $this->form->getInputFilter());
    }

    /**
     * @return void
     * @group set-data
     */
    public function testCanBindNestedCollectionAfterPrepare2(): void
    {

        $collection = new Element\Collection('numbers');
        $collection->setOptions([
            'count'          => 2,
            'allow_add'      => false,
            'allow_remove'   => false,
            'target_element' => [
                'type' => TestAsset\PhoneFieldset::class,
            ],
        ]);

        $form   = new Form();
        $object = new ArrayObject();
        $phone1 = new TestAsset\Entity\Phone();
        $phone2 = new TestAsset\Entity\Phone();
        $phone1->setNumber('unmodified');
        $phone2->setNumber('unmodified');
        $collection->setObject([$phone1, $phone2]);

        $form->add($collection);
        $form->setObject($object);

        $value = [
            'numbers' => [
                [
                    'number' => 'modified',
                    'id'     => '1',
                ],
                [
                    'number' => 'modified',
                    'id'     => '2',
                ],
            ],
        ];

        $form->bindValues($value);
        $form->prepare();

        $fieldsets = $collection->getFieldsets();

        $fieldsetFoo = $fieldsets[0];
        $fieldsetBar = $fieldsets[1];

        $elementsFoo = $fieldsetFoo->getElements();
        $elementsBar = $fieldsetBar->getElements();

        self::assertCount(2, $elementsFoo);
        self::assertCount(2, $elementsBar);

        self::assertEquals('numbers[0][id]', $elementsFoo['id']->getName());
        self::assertEquals('numbers[0][number]', $elementsFoo['number']->getName());
        self::assertEquals('numbers[1][id]', $elementsBar['id']->getName());
        self::assertEquals('numbers[1][number]', $elementsBar['number']->getName());

        self::assertEquals($value['numbers'][0]['number'], $fieldsetFoo->getObject()->getNumber());
        self::assertEquals($value['numbers'][1]['number'], $fieldsetBar->getObject()->getNumber());

        self::assertTrue($form->isValid());

        $form->setData($value);
        $form->prepare();

        self::assertEquals('numbers[0][id]', $elementsFoo['id']->getName());
        self::assertEquals('numbers[0][number]', $elementsFoo['number']->getName());
        self::assertEquals('numbers[1][id]', $elementsBar['id']->getName());
        self::assertEquals('numbers[1][number]', $elementsBar['number']->getName());
    }
}
