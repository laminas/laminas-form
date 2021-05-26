<?php

namespace LaminasTest\Form\Element;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Element\Collection;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\ArrayInput;
use LaminasTest\Form\TestAsset\AddressFieldset;
use LaminasTest\Form\TestAsset\ArrayModel;
use LaminasTest\Form\TestAsset\CategoryFieldset;
use LaminasTest\Form\TestAsset\CountryFieldset;
use LaminasTest\Form\TestAsset\CustomCollection;
use LaminasTest\Form\TestAsset\CustomTraversable;
use LaminasTest\Form\TestAsset\Entity\Address;
use LaminasTest\Form\TestAsset\Entity\Category;
use LaminasTest\Form\TestAsset\Entity\City;
use LaminasTest\Form\TestAsset\Entity\Country;
use LaminasTest\Form\TestAsset\Entity\Phone;
use LaminasTest\Form\TestAsset\Entity\Product;
use LaminasTest\Form\TestAsset\FormCollection;
use LaminasTest\Form\TestAsset\PhoneFieldset;
use LaminasTest\Form\TestAsset\ProductFieldset;
use PHPUnit\Framework\TestCase;
use stdClass;

use function class_exists;
use function count;
use function extension_loaded;
use function iterator_count;
use function spl_object_hash;

class CollectionTest extends TestCase
{
    /** @var FormCollection  */
    protected $form;
    /** @var ProductFieldset  */
    protected $productFieldset;

    protected function setUp(): void
    {
        $this->form            = new FormCollection();
        $this->productFieldset = new ProductFieldset();

        parent::setUp();
    }

    public function testCanRetrieveDefaultPlaceholder()
    {
        $placeholder = $this->form->get('colors')->getTemplatePlaceholder();
        $this->assertEquals('__index__', $placeholder);
    }

    public function testCannotAllowNewElementsIfAllowAddIsFalse()
    {
        $collection = $this->form->get('colors');

        $this->assertTrue($collection->allowAdd());
        $collection->setAllowAdd(false);
        $this->assertFalse($collection->allowAdd());

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());

        $this->expectException(DomainException::class);
        $data[] = 'orange';
        $collection->populateValues($data);
    }

    public function testCanAddNewElementsIfAllowAddIsTrue()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowAdd(true);
        $this->assertTrue($collection->allowAdd());

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());

        $data[] = 'orange';
        $collection->populateValues($data);
        $this->assertCount(3, $collection->getElements());
    }

    public function testCanRemoveElementsIfAllowRemoveIsTrue()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowRemove(true);
        $this->assertTrue($collection->allowRemove());

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());

        unset($data[0]);

        $collection->populateValues($data);
        $this->assertCount(1, $collection->getElements());
    }

    public function testCanReplaceElementsIfAllowAddAndAllowRemoveIsTrue()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowAdd(true);
        $collection->setAllowRemove(true);

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());

        unset($data[0]);
        $data[] = 'orange';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());
    }

    public function testCanValidateFormWithCollectionWithoutTemplate()
    {
        $this->form->setData([
            'colors'    => [
                '#ffffff',
                '#ffffff',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testCannotValidateFormWithCollectionWithBadColor()
    {
        $this->form->setData([
            'colors'    => [
                '#ffffff',
                '123465',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(false, $this->form->isValid());
        $messages = $this->form->getMessages();
        $this->assertArrayHasKey('colors', $messages);
    }

    public function testCannotValidateFormWithCollectionWithBadFieldsetField()
    {
        $this->form->setData([
            'colors'    => [
                '#ffffff',
                '#ffffff',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => null,
                    ],
                ],
            ],
        ]);

        $this->assertEquals(false, $this->form->isValid());
        $messages = $this->form->getMessages();
        $this->assertCount(1, $messages);
        $this->assertArrayHasKey('fieldsets', $messages);
    }

    public function testCanValidateFormWithCollectionWithTemplate()
    {
        $collection = $this->form->get('colors');

        $this->assertFalse($collection->shouldCreateTemplate());
        $collection->setShouldCreateTemplate(true);
        $this->assertTrue($collection->shouldCreateTemplate());

        $collection->setTemplatePlaceholder('__template__');

        $this->form->setData([
            'colors'    => [
                '#ffffff',
                '#ffffff',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testThrowExceptionIfThereAreLessElementsAndAllowRemoveNotAllowed()
    {
        $this->expectException(DomainException::class);

        $collection = $this->form->get('colors');
        $collection->setAllowRemove(false);

        $this->form->setData([
            'colors'    => [
                '#ffffff',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
            ],
        ]);

        $this->form->isValid();
    }

    public function testCanValidateLessThanSpecifiedCount()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowRemove(true);

        $this->form->setData([
            'colors'    => [
                '#ffffff',
            ],
            'fieldsets' => [
                [
                    'field'           => 'oneValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
                [
                    'field'           => 'twoValue',
                    'nested_fieldset' => [
                        'anotherField' => 'anotherValue',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testSetOptions()
    {
        $collection = $this->form->get('colors');
        $element    = new Element('foo');
        $collection->setOptions([
            'target_element'         => $element,
            'count'                  => 2,
            'allow_add'              => true,
            'allow_remove'           => false,
            'should_create_template' => true,
            'template_placeholder'   => 'foo',
        ]);

        $this->assertInstanceOf(Element::class, $collection->getOption('target_element'));
        $this->assertEquals(2, $collection->getOption('count'));
        $this->assertEquals(true, $collection->getOption('allow_add'));
        $this->assertEquals(false, $collection->getOption('allow_remove'));
        $this->assertEquals(true, $collection->getOption('should_create_template'));
        $this->assertEquals('foo', $collection->getOption('template_placeholder'));
    }

    public function testSetOptionsTraversable()
    {
        $collection = $this->form->get('colors');
        $element    = new Element('foo');
        $collection->setOptions(new CustomTraversable([
            'target_element'         => $element,
            'count'                  => 2,
            'allow_add'              => true,
            'allow_remove'           => false,
            'should_create_template' => true,
            'template_placeholder'   => 'foo',
        ]));

        $this->assertInstanceOf(Element::class, $collection->getOption('target_element'));
        $this->assertEquals(2, $collection->getOption('count'));
        $this->assertEquals(true, $collection->getOption('allow_add'));
        $this->assertEquals(false, $collection->getOption('allow_remove'));
        $this->assertEquals(true, $collection->getOption('should_create_template'));
        $this->assertEquals('foo', $collection->getOption('template_placeholder'));
    }

    public function testSetObjectNullRaisesException()
    {
        $collection = $this->form->get('colors');
        $this->expectException(InvalidArgumentException::class);
        $collection->setObject(null);
    }

    public function testSetTargetElementNullRaisesException()
    {
        $collection = $this->form->get('colors');
        $this->expectException(InvalidArgumentException::class);
        $collection->setTargetElement(null);
    }

    public function testGetTargetElement()
    {
        $collection = $this->form->get('colors');
        $element    = new Element('foo');
        $collection->setTargetElement($element);

        $this->assertInstanceOf(Element::class, $collection->getTargetElement());
    }

    public function testExtractFromObjectDoesntTouchOriginalObject()
    {
        $form = new Form();
        $form->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );
        $this->productFieldset->setUseAsBaseFieldset(true);
        $form->add($this->productFieldset);

        $originalObjectHash = spl_object_hash(
            $this->productFieldset->get('categories')->getTargetElement()->getObject()
        );

        $product = new Product();
        $product->setName('foo');
        $product->setPrice(42);
        $cat1 = new Category();
        $cat1->setName('bar');
        $cat2 = new Category();
        $cat2->setName('bar2');

        $product->setCategories([$cat1, $cat2]);

        $form->bind($product);

        $form->setData([
            'product' => [
                'name'       => 'franz',
                'price'      => 13,
                'categories' => [
                    ['name' => 'sepp'],
                    ['name' => 'herbert'],
                ],
            ],
        ]);

        $objectAfterExtractHash = spl_object_hash(
            $this->productFieldset->get('categories')->getTargetElement()->getObject()
        );

        $this->assertSame($originalObjectHash, $objectAfterExtractHash);
    }

    public function testDoesNotCreateNewObjects()
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new Form();
        $form->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );
        $this->productFieldset->setUseAsBaseFieldset(true);
        $form->add($this->productFieldset);

        $product = new Product();
        $product->setName('foo');
        $product->setPrice(42);
        $cat1 = new Category();
        $cat1->setName('bar');
        $cat2 = new Category();
        $cat2->setName('bar2');

        $product->setCategories([$cat1, $cat2]);

        $form->bind($product);

        $form->setData([
            'product' => [
                'name'       => 'franz',
                'price'      => 13,
                'categories' => [
                    ['name' => 'sepp'],
                    ['name' => 'herbert'],
                ],
            ],
        ]);
        $form->isValid();

        $categories = $product->getCategories();
        $this->assertSame($categories[0], $cat1);
        $this->assertSame($categories[1], $cat2);
    }

    public function testCreatesNewObjectsIfSpecified()
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->productFieldset->setUseAsBaseFieldset(true);
        $categories = $this->productFieldset->get('categories');
        $categories->setOptions([
            'create_new_objects' => true,
        ]);

        $form = new Form();
        $form->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );
        $form->add($this->productFieldset);

        $product = new Product();
        $product->setName('foo');
        $product->setPrice(42);
        $cat1 = new Category();
        $cat1->setName('bar');
        $cat2 = new Category();
        $cat2->setName('bar2');

        $product->setCategories([$cat1, $cat2]);

        $form->bind($product);

        $form->setData([
            'product' => [
                'name'       => 'franz',
                'price'      => 13,
                'categories' => [
                    ['name' => 'sepp'],
                    ['name' => 'herbert'],
                ],
            ],
        ]);
        $form->isValid();

        $categories = $product->getCategories();
        $this->assertNotSame($categories[0], $cat1);
        $this->assertNotSame($categories[1], $cat2);
    }

    /**
     * @group issue-6585
     * @group issue-6614
     */
    public function testAddingCollectionElementAfterBind()
    {
        $form = new Form();
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );

        $phone = new PhoneFieldset();

        $form->add([
            'name'    => 'phones',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $phone,
                'count'          => 1,
                'allow_add'      => true,
            ],
        ]);

        $data = [
            'phones' => [
                ['number' => '1234567'],
                ['number' => '1234568'],
            ],
        ];

        $phone = new Phone();
        $phone->setNumber($data['phones'][0]['number']);

        $customer         = new stdClass();
        $customer->phones = [$phone];

        $form->bind($customer);
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    /**
     * @group issue-6585
     * @group issue-6614
     */
    public function testDoesNotCreateNewObjectsWhenUsingNestedCollections()
    {
        $addressesFieldset = new AddressFieldset();
        $addressesFieldset->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );
        $addressesFieldset->remove('city');

        $form = new Form();
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $form->add([
            'name'    => 'addresses',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $addressesFieldset,
                'count'          => 1,
            ],
        ]);

        $data = [
            'addresses' => [
                [
                    'street' => 'street1',
                    'phones' => [
                        ['number' => '1234567'],
                    ],
                ],
            ],
        ];

        $phone = new Phone();
        $phone->setNumber($data['addresses'][0]['phones'][0]['number']);

        $address = new Address();
        $address->setStreet($data['addresses'][0]['street']);
        $address->setPhones([$phone]);

        $customer            = new stdClass();
        $customer->addresses = [$address];

        $form->bind($customer);
        $form->setData($data);

        $this->assertTrue($form->isValid());
        $phones = $customer->addresses[0]->getPhones();
        $this->assertSame($phone, $phones[0]);
    }

    public function testDoNotCreateExtraFieldsetOnMultipleBind()
    {
        $form = new Form();
        $this->productFieldset->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );
        $form->add($this->productFieldset);
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );

        $product    = new Product();
        $categories = [
            new Category(),
            new Category(),
        ];
        $product->setCategories($categories);

        $market          = new stdClass();
        $market->product = $product;

        // this will pass the test
        $form->bind($market);
        $this->assertSame(count($categories), iterator_count($form->get('product')->get('categories')->getIterator()));

        // this won't pass, but must
        $form->bind($market);
        $this->assertSame(count($categories), iterator_count($form->get('product')->get('categories')->getIterator()));
    }

    public function testExtractDefaultIsEmptyArray()
    {
        $collection = $this->form->get('fieldsets');
        $this->assertEquals([], $collection->extract());
    }

    public function testExtractThroughTargetElementHydrator()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $expected = [
            'obj2' => ['field' => 'fieldOne'],
            'obj3' => ['field' => 'fieldTwo'],
        ];

        $this->assertEquals($expected, $collection->extract());
    }

    public function testExtractMaintainsTargetElementObject()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $expected = $collection->getTargetElement()->getObject();

        $collection->extract();

        $test = $collection->getTargetElement()->getObject();

        $this->assertSame($expected, $test);
    }

    public function testExtractThroughCustomHydrator()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $mockHydrator = $this->createMock(HydratorInterface::class);
        $mockHydrator->expects($this->exactly(2))
            ->method('extract')
            ->willReturnCallback(static function ($object) {
                return ['value' => $object->field . '_foo'];
            });

        $collection->setHydrator($mockHydrator);

        $expected = [
            'obj2' => ['value' => 'fieldOne_foo'],
            'obj3' => ['value' => 'fieldTwo_foo'],
        ];

        $this->assertEquals($expected, $collection->extract());
    }

    public function testExtractFromTraversable()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $traversable = new ArrayObject($collection->getObject());
        $collection->setObject($traversable);

        $expected = [
            'obj2' => ['field' => 'fieldOne'],
            'obj3' => ['field' => 'fieldTwo'],
        ];

        $this->assertEquals($expected, $collection->extract());
    }

    public function testValidateData()
    {
        $myFieldset = new Fieldset();
        $myFieldset->add([
            'name' => 'email',
            'type' => 'Email',
        ]);

        $myForm = new Form();
        $myForm->add([
            'name'    => 'collection',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $myFieldset,
            ],
        ]);

        $data = [
            'collection' => [
                ['email' => 'test1@test1.com'],
                ['email' => 'test2@test2.com'],
                ['email' => 'test3@test3.com'],
            ],
        ];

        $myForm->setData($data);

        $this->assertTrue($myForm->isValid());
        $this->assertEmpty($myForm->getMessages());
    }

    protected function prepareForExtract(Collection $collection)
    {
        $targetElement = $collection->getTargetElement();

        $obj1 = new stdClass();

        $targetElement
            ->setHydrator(
                class_exists(ObjectPropertyHydrator::class)
                    ? new ObjectPropertyHydrator()
                    : new ObjectProperty()
            )
            ->setObject($obj1);

        $obj2        = new stdClass();
        $obj2->field = 'fieldOne';

        $obj3        = new stdClass();
        $obj3->field = 'fieldTwo';

        $collection->setObject([
            'obj2' => $obj2,
            'obj3' => $obj3,
        ]);
    }

    public function testCollectionCanBindObjectAndPopulateAndExtractNestedFieldsets()
    {
        $productFieldset = new ProductFieldset();
        $productFieldset->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );

        $mainFieldset = new Fieldset();
        $mainFieldset->setObject(new stdClass());
        $mainFieldset->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $mainFieldset->add($productFieldset);

        $form = new Form();
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $form->add([
            'name'    => 'collection',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $mainFieldset,
                'count'          => 2,
            ],
        ]);

        $market = new stdClass();

        $prices           = [100, 200];
        $categoryNames    = ['electronics', 'furniture'];
        $productCountries = ['Russia', 'Jamaica'];

        $shop1          = new stdClass();
        $shop1->product = new Product();
        $shop1->product->setPrice($prices[0]);

        $category = new Category();
        $category->setName($categoryNames[0]);
        $shop1->product->setCategories([$category]);

        $country = new Country();
        $country->setName($productCountries[0]);
        $shop1->product->setMadeInCountry($country);

        $shop2          = new stdClass();
        $shop2->product = new Product();
        $shop2->product->setPrice($prices[1]);

        $category = new Category();
        $category->setName($categoryNames[1]);
        $shop2->product->setCategories([$category]);

        $country = new Country();
        $country->setName($productCountries[1]);
        $shop2->product->setMadeInCountry($country);

        $market->collection = [$shop1, $shop2];
        $form->bind($market);

        //test for object binding
        $marketCollection = $form->get('collection');
        $this->assertInstanceOf(Collection::class, $marketCollection);

        foreach ($marketCollection as $shopFieldset) {
            $this->assertInstanceOf(Fieldset::class, $shopFieldset);
            $this->assertInstanceOf('stdClass', $shopFieldset->getObject());

            // test for collection -> fieldset
            $productFieldset = $shopFieldset->get('product');
            $this->assertInstanceOf(ProductFieldset::class, $productFieldset);
            $this->assertInstanceOf(Product::class, $productFieldset->getObject());

            // test for collection -> fieldset -> fieldset
            $this->assertInstanceOf(
                CountryFieldset::class,
                $productFieldset->get('made_in_country')
            );
            $this->assertInstanceOf(
                Country::class,
                $productFieldset->get('made_in_country')->getObject()
            );

            // test for collection -> fieldset -> collection
            $productCategories = $productFieldset->get('categories');
            $this->assertInstanceOf(Collection::class, $productCategories);

            // test for collection -> fieldset -> collection -> fieldset
            foreach ($productCategories as $category) {
                $this->assertInstanceOf(CategoryFieldset::class, $category);
                $this->assertInstanceOf(Category::class, $category->getObject());
            }
        }

        // test for correct extract and populate form values
        // test for collection -> fieldset -> field value
        foreach ($prices as $key => $price) {
            $this->assertEquals(
                $price,
                $form->get('collection')->get($key)
                    ->get('product')
                    ->get('price')
                    ->getValue()
            );
        }

        // test for collection -> fieldset -> fieldset ->field value
        foreach ($productCountries as $key => $countryName) {
            $this->assertEquals(
                $countryName,
                $form->get('collection')->get($key)
                    ->get('product')
                    ->get('made_in_country')
                    ->get('name')
                    ->getValue()
            );
        }

        // test collection -> fieldset -> collection -> fieldset -> field value
        foreach ($categoryNames as $key => $categoryName) {
            $this->assertEquals(
                $categoryName,
                $form->get('collection')->get($key)
                    ->get('product')
                    ->get('categories')->get(0)
                    ->get('name')->getValue()
            );
        }
    }

    public function testExtractFromTraversableImplementingToArrayThroughCollectionHydrator()
    {
        $collection = $this->form->get('fieldsets');

        // this test is using a hydrator set on the collection
        $collection->setHydrator(
            class_exists(ArraySerializableHydrator::class)
                ? new ArraySerializableHydrator()
                : new ArraySerializable()
        );

        $this->prepareForExtractWithCustomTraversable($collection);

        $expected = [
            ['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1'],
            ['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2'],
        ];

        $this->assertEquals($expected, $collection->extract());
    }

    public function testExtractFromTraversableImplementingToArrayThroughTargetElementHydrator()
    {
        $collection = $this->form->get('fieldsets');

        // this test is using a hydrator set on the target element of the collection
        $targetElement = $collection->getTargetElement();
        $targetElement->setHydrator(
            class_exists(ArraySerializableHydrator::class)
                ? new ArraySerializableHydrator()
                : new ArraySerializable()
        );
        $obj1 = new ArrayModel();
        $targetElement->setObject($obj1);

        $this->prepareForExtractWithCustomTraversable($collection);

        $expected = [
            ['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1'],
            ['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2'],
        ];

        $this->assertEquals($expected, $collection->extract());
    }

    protected function prepareForExtractWithCustomTraversable(Collection $collection)
    {
        $obj2 = new ArrayModel();
        $obj2->exchangeArray(['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1']);
        $obj3 = new ArrayModel();
        $obj3->exchangeArray(['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2']);

        $traversable = new CustomCollection();
        $traversable->append($obj2);
        $traversable->append($obj3);
        $collection->setObject($traversable);
    }

    public function testPopulateValuesWithFirstKeyGreaterThanZero()
    {
        $inputData = [
            1 => ['name' => 'black'],
            5 => ['name' => 'white'],
        ];

        // Standalone Collection element
        $collection = new Collection('fieldsets', [
            'count'          => 1,
            'target_element' => new CategoryFieldset(),
        ]);

        $form = new Form();
        $form->add([
            'type'    => Collection::class,
            'name'    => 'collection',
            'options' => [
                'count'          => 1,
                'target_element' => new CategoryFieldset(),
            ],
        ]);

        // Collection element attached to a form
        $formCollection = $form->get('collection');

        $collection->populateValues($inputData);
        $formCollection->populateValues($inputData);

        $this->assertCount(count($collection->getFieldsets()), $inputData);
        $this->assertCount(count($formCollection->getFieldsets()), $inputData);
    }

    public function testCanRemoveAllElementsIfAllowRemoveIsTrue()
    {
        /**
         * @var Collection $collection
         */
        $collection = $this->form->get('colors');
        $collection->setAllowRemove(true);
        $collection->setCount(0);

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertCount(2, $collection->getElements());

        $collection->populateValues([]);
        $this->assertCount(0, $collection->getElements());
    }

    public function testCanBindObjectMultipleNestedFieldsets()
    {
        $productFieldset = new ProductFieldset();
        $productFieldset->setHydrator(
            class_exists(ArraySerializableHydrator::class)
                ? new ArraySerializableHydrator()
                : new ArraySerializable()
        );
        $productFieldset->setObject(new Product());

        $nestedFieldset = new Fieldset('nested');
        $nestedFieldset->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $nestedFieldset->setObject(new stdClass());
        $nestedFieldset->add([
            'name'    => 'products',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $productFieldset,
                'count'          => 2,
            ],
        ]);

        $mainFieldset = new Fieldset('main');
        $mainFieldset->setUseAsBaseFieldset(true);
        $mainFieldset->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $mainFieldset->setObject(new stdClass());
        $mainFieldset->add([
            'name'    => 'nested',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $nestedFieldset,
                'count'          => 2,
            ],
        ]);

        $form = new Form();
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $form->add($mainFieldset);

        $market = new stdClass();

        $prices = [100, 200];

        $products[0] = new Product();
        $products[0]->setPrice($prices[0]);
        $products[1] = new Product();
        $products[1]->setPrice($prices[1]);

        $shop[0]           = new stdClass();
        $shop[0]->products = $products;

        $shop[1]           = new stdClass();
        $shop[1]->products = $products;

        $market->nested = $shop;
        $form->bind($market);

        //test for object binding

        // Main fieldset has a collection 'nested'...
        $this->assertCount(1, $form->get('main')->getFieldsets());
        foreach ($form->get('main')->getFieldsets() as $fieldset) {
            // ...which contains two stdClass objects (shops)
            $this->assertCount(2, $fieldset->getFieldsets());
            foreach ($fieldset->getFieldsets() as $nestedfieldset) {
                // Each shop is represented by a single fieldset
                $this->assertCount(1, $nestedfieldset->getFieldsets());
                foreach ($nestedfieldset->getFieldsets() as $productfieldset) {
                    // Each shop fieldset contain a collection with two products in it
                    $this->assertCount(2, $productfieldset->getFieldsets());
                    foreach ($productfieldset->getFieldsets() as $product) {
                        $this->assertInstanceOf(Product::class, $product->getObject());
                    }
                }
            }
        }
    }

    public function testNestedCollections()
    {
        // @see https://github.com/zendframework/zf2/issues/5640
        $addressesFieldeset = new AddressFieldset();
        $addressesFieldeset->setHydrator(
            class_exists(ClassMethodsHydrator::class)
                ? new ClassMethodsHydrator()
                : new ClassMethods()
        );

        $form = new Form();
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $form->add([
            'name'    => 'addresses',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $addressesFieldeset,
                'count'          => 2,
            ],
        ]);

        $data = [
            ['number' => '0000000001', 'street' => 'street1'],
            ['number' => '0000000002', 'street' => 'street2'],
        ];

        $phone1 = new Phone();
        $phone1->setNumber($data[0]['number']);

        $phone2 = new Phone();
        $phone2->setNumber($data[1]['number']);

        $address1 = new Address();
        $address1->setStreet($data[0]['street']);
        $address1->setPhones([$phone1]);

        $address2 = new Address();
        $address2->setStreet($data[1]['street']);
        $address2->setPhones([$phone2]);

        $customer            = new stdClass();
        $customer->addresses = [$address1, $address2];

        $form->bind($customer);

        //test for object binding
        foreach ($form->get('addresses')->getFieldsets() as $fieldset) {
            $this->assertInstanceOf(Address::class, $fieldset->getObject());
            foreach ($fieldset->getFieldsets() as $childFieldsetName => $childFieldset) {
                switch ($childFieldsetName) {
                    case 'city':
                        $this->assertInstanceOf(City::class, $childFieldset->getObject());
                        break;
                    case 'phones':
                        foreach ($childFieldset->getFieldsets() as $phoneFieldset) {
                            $this->assertInstanceOf(
                                Phone::class,
                                $phoneFieldset->getObject()
                            );
                        }
                        break;
                }
            }
        }

        //test for correct extract and populate
        $index = 0;
        foreach ($form->get('addresses') as $addresses) {
            $this->assertEquals($data[$index]['street'], $addresses->get('street')->getValue());
            //assuming data has just 1 phone entry
            foreach ($addresses->get('phones') as $phone) {
                $this->assertEquals($data[$index]['number'], $phone->get('number')->getValue());
            }
            $index++;
        }
    }

    public function testSetDataOnFormPopulatesCollection()
    {
        $form = new Form();
        $form->add([
            'name'    => 'names',
            'type'    => 'Collection',
            'options' => [
                'target_element' => new Element\Text(),
            ],
        ]);

        $names = ['foo', 'bar', 'baz', 'bat'];

        $form->setData([
            'names' => $names,
        ]);

        $this->assertCount(count($names), $form->get('names'));

        $i = 0;
        foreach ($form->get('names') as $field) {
            $this->assertEquals($names[$i], $field->getValue());
            $i++;
        }
    }

    public function testSettingSomeDataButNoneForCollectionReturnsSpecifiedNumberOfElementsAfterPrepare()
    {
        $form = new Form();
        $form->add(new Element\Text('input'));
        $form->add([
            'name'    => 'names',
            'type'    => 'Collection',
            'options' => [
                'target_element' => new Element\Text(),
                'count'          => 2,
            ],
        ]);

        $form->setData([
            'input' => 'foo',
        ]);

        $this->assertCount(0, $form->get('names'));

        $form->prepare();

        $this->assertCount(2, $form->get('names'));
    }

    public function testMininumLenghtIsMaintanedWhenSettingASmallerCollection()
    {
        $arrayCollection = [
            new Element\Color(),
            new Element\Color(),
        ];

        $collection = $this->form->get('colors');
        $collection->setCount(3);
        $collection->setObject($arrayCollection);
        $this->assertEquals(3, $collection->getCount());
    }

    /**
     * @group issue-6263
     * @group issue-6518
     */
    public function testCollectionProperlyHandlesAddingObjectsOfTypeElementInterface()
    {
        $form = new Form('test');
        $text = new Element\Text('text');
        $form->add([
            'name'    => 'text',
            'type'    => Collection::class,
            'options' => [
                'target_element' => $text,
                'count'          => 2,
            ],
        ]);
        $object = new ArrayObject(['text' => ['Foo', 'Bar']]);
        $form->bind($object);
        $this->assertTrue($form->isValid());

        $result = $form->getData();
        $this->assertInstanceOf('ArrayAccess', $result);
        $this->assertArrayHasKey('text', $result);
        $this->assertIsArray($result['text']);
        $this->assertArrayHasKey(0, $result['text']);
        $this->assertEquals('Foo', $result['text'][0]);
        $this->assertArrayHasKey(1, $result['text']);
        $this->assertEquals('Bar', $result['text'][1]);
    }

    /**
     * Unit test to ensure behavior of extract() method is unaffected by refactor
     *
     * @group issue-6263
     * @group issue-6518
     */
    public function testCollectionShouldSilentlyIgnorePopulatingFieldsetWithDisallowedObject()
    {
        $mainFieldset = new Fieldset();
        $mainFieldset->add(new Element\Text('test'));
        $mainFieldset->setObject(new ArrayObject());
        $mainFieldset->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );

        $form = new Form();
        $form->setObject(new stdClass());
        $form->setHydrator(
            class_exists(ObjectPropertyHydrator::class)
                ? new ObjectPropertyHydrator()
                : new ObjectProperty()
        );
        $form->add([
            'name'    => 'collection',
            'type'    => 'Collection',
            'options' => [
                'target_element' => $mainFieldset,
                'count'          => 2,
            ],
        ]);

        $model             = new stdClass();
        $model->collection = [new ArrayObject(['test' => 'bar']), new stdClass()];

        $form->bind($model);
        $this->assertTrue($form->isValid());

        $result = $form->getData();
        $this->assertInstanceOf('stdClass', $result);
        $this->assertObjectHasAttribute('collection', $result);
        $this->assertIsArray($result->collection);
        $this->assertCount(1, $result->collection);
        $this->assertInstanceOf('ArrayObject', $result->collection[0]);
        $this->assertArrayHasKey('test', $result->collection[0]);
        $this->assertEquals('bar', $result->collection[0]['test']);
    }

    /**
     * @group issue-6263
     * @group issue-6298
     */
    public function testCanHydrateObject()
    {
        $form   = $this->form;
        $object = new ArrayObject();
        $form->bind($object);
        $data = [
            'colors' => [
                '#ffffff',
            ],
        ];
        $form->setData($data);
        $this->assertTrue($form->isValid());
        $this->assertIsArray($object['colors']);
        $this->assertCount(1, $object['colors']);
    }

    public function testCanRemoveMultipleElements()
    {
        /**
         * @var Collection $collection
         */
        $collection = $this->form->get('colors');
        $collection->setAllowRemove(true);
        $collection->setCount(0);

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';
        $data[] = 'red';

        $collection->populateValues($data);

        $collection->populateValues(['colors' => ['0' => 'blue']]);
        $this->assertCount(1, $collection->getElements());
    }

    public function testGetErrorMessagesForInvalidCollectionElements()
    {
        // Configure InputFilter
        $inputFilter = $this->form->getInputFilter();
        $inputFilter->add(
            [
                'name'     => 'colors',
                'type'     => ArrayInput::class,
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'fieldsets',
                'type'     => ArrayInput::class,
                'required' => true,
            ]
        );

        $this->form->setData([]);
        $this->form->isValid();

        $this->assertEquals(
            [
                'colors'    => [
                    'isEmpty' => "Value is required and can't be empty",
                ],
                'fieldsets' => [
                    'isEmpty' => "Value is required and can't be empty",
                ],
            ],
            $this->form->getMessages()
        );
    }

    /**
     * @see https://github.com/zendframework/zend-form/pull/230
     */
    public function testNullTargetElementShouldResultInEmptyData()
    {
        $form = new Form();

        $form->add([
            'type'    => Collection::class,
            'name'    => 'fieldsets',
            'options' => [
                'count' => 2,
            ],
        ]);

        $collection = $form->get('fieldsets');
        $data       = [
            'fieldsets' => [
                'red',
                'green',
                'blue',
            ],
        ];

        $form->setData($data);
        $form->isValid();

        // expect the fieldsets key to be an empty array since there's no valid targetElement
        $this->assertEquals(
            [
                'fieldsets' => [],
            ],
            $form->getData()
        );
    }

    public function testPopulateValuesTraversable()
    {
        $data = new CustomTraversable(['blue', 'green']);

        $collection = $this->form->get('colors');
        $collection->setAllowRemove(false);
        $collection->populateValues($data);

        $this->assertCount(2, $collection->getElements());
    }

    public function testSetObjectTraversable()
    {
        $collection = $this->form->get('fieldsets');

        // this test is using a hydrator set on the target element of the collection
        $targetElement = $collection->getTargetElement();
        $targetElement->setHydrator(
            class_exists(ArraySerializableHydrator::class)
                ? new ArraySerializableHydrator()
                : new ArraySerializable()
        );
        $obj1 = new ArrayModel();
        $targetElement->setObject($obj1);

        $obj2 = new ArrayModel();
        $obj2->exchangeArray(['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1']);
        $obj3 = new ArrayModel();
        $obj3->exchangeArray(['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2']);

        $collection->setObject(new CustomTraversable([$obj2, $obj3]));

        $expected = [
            ['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1'],
            ['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2'],
        ];

        $this->assertSame($expected, $collection->extract());
        $this->assertSame([$obj2, $obj3], $collection->getObject());
    }
}
