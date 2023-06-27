<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use ArrayAccess;
use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Element\Collection;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\Form;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
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

use function count;
use function extension_loaded;
use function iterator_count;
use function spl_object_hash;

final class CollectionTest extends TestCase
{
    private FormCollection $form;
    private ProductFieldset $productFieldset;

    protected function setUp(): void
    {
        $this->form            = new FormCollection();
        $this->productFieldset = new ProductFieldset();

        parent::setUp();
    }

    public function testCanRetrieveDefaultPlaceholder(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $placeholder = $collection->getTemplatePlaceholder();
        self::assertEquals('__index__', $placeholder);
    }

    public function testCannotAllowNewElementsIfAllowAddIsFalse(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);

        self::assertTrue($collection->allowAdd());
        $collection->setAllowAdd(false);
        self::assertFalse($collection->allowAdd());

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());

        $this->expectException(DomainException::class);
        $data[] = 'orange';
        $collection->populateValues($data);
    }

    public function testCanAddNewElementsIfAllowAddIsTrue(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowAdd(true);
        self::assertTrue($collection->allowAdd());

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());

        $data[] = 'orange';
        $collection->populateValues($data);
        self::assertCount(3, $collection->getElements());
    }

    public function testCanRemoveElementsIfAllowRemoveIsTrue(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowRemove(true);
        self::assertTrue($collection->allowRemove());

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());

        unset($data[0]);

        $collection->populateValues($data);
        self::assertCount(1, $collection->getElements());
    }

    public function testCanReplaceElementsIfAllowAddAndAllowRemoveIsTrue(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowAdd(true);
        $collection->setAllowRemove(true);

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());

        unset($data[0]);
        $data[] = 'orange';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());
    }

    public function testCanValidateFormWithCollectionWithoutTemplate(): void
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

        self::assertEquals(true, $this->form->isValid());
    }

    public function testCannotValidateFormWithCollectionWithBadColor(): void
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

        self::assertEquals(false, $this->form->isValid());
        $messages = $this->form->getMessages();
        self::assertArrayHasKey('colors', $messages);
    }

    public function testCannotValidateFormWithCollectionWithBadFieldsetField(): void
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

        self::assertEquals(false, $this->form->isValid());
        $messages = $this->form->getMessages();
        self::assertCount(1, $messages);
        self::assertArrayHasKey('fieldsets', $messages);
    }

    public function testCanValidateFormWithCollectionWithTemplate(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);

        self::assertFalse($collection->shouldCreateTemplate());
        $collection->setShouldCreateTemplate(true);
        self::assertTrue($collection->shouldCreateTemplate());

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

        self::assertEquals(true, $this->form->isValid());
    }

    public function testThrowExceptionIfThereAreLessElementsAndAllowRemoveNotAllowed(): void
    {
        $this->expectException(DomainException::class);

        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
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

    public function testCanValidateLessThanSpecifiedCount(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
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

        self::assertEquals(true, $this->form->isValid());
    }

    public function testSetOptions(): void
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

        self::assertInstanceOf(Element::class, $collection->getOption('target_element'));
        self::assertEquals(2, $collection->getOption('count'));
        self::assertEquals(true, $collection->getOption('allow_add'));
        self::assertEquals(false, $collection->getOption('allow_remove'));
        self::assertEquals(true, $collection->getOption('should_create_template'));
        self::assertEquals('foo', $collection->getOption('template_placeholder'));
    }

    public function testSetOptionsTraversable(): void
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

        self::assertInstanceOf(Element::class, $collection->getOption('target_element'));
        self::assertEquals(2, $collection->getOption('count'));
        self::assertEquals(true, $collection->getOption('allow_add'));
        self::assertEquals(false, $collection->getOption('allow_remove'));
        self::assertEquals(true, $collection->getOption('should_create_template'));
        self::assertEquals('foo', $collection->getOption('template_placeholder'));
    }

    public function testSetObjectNullRaisesException(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress NullArgument */
        $collection->setObject(null);
    }

    public function testSetTargetElementNullRaisesException(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $this->expectException(InvalidArgumentException::class);
        $collection->setTargetElement(null);
    }

    public function testGetTargetElement(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $element = new Element('foo');
        $collection->setTargetElement($element);

        self::assertInstanceOf(Element::class, $collection->getTargetElement());
    }

    public function testExtractFromObjectDoesntTouchOriginalObject(): void
    {
        $form = new Form();
        $form->setHydrator(new ClassMethodsHydrator());
        $this->productFieldset->setUseAsBaseFieldset(true);
        $form->add($this->productFieldset);

        $categories = $this->productFieldset->get('categories');
        self::assertInstanceOf(Collection::class, $categories);
        $targetElement = $categories->getTargetElement();
        self::assertInstanceOf(FieldsetInterface::class, $targetElement);
        $originalObjectHash = spl_object_hash(
            $targetElement->getObject()
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
            $targetElement->getObject()
        );

        self::assertSame($originalObjectHash, $objectAfterExtractHash);
    }

    public function testDoesNotCreateNewObjects(): void
    {
        if (! extension_loaded('intl')) {
            // Required by \Laminas\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new Form();
        $form->setHydrator(new ClassMethodsHydrator());
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
        self::assertSame($categories[0], $cat1);
        self::assertSame($categories[1], $cat2);
    }

    public function testCreatesNewObjectsIfSpecified(): void
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
        $form->setHydrator(new ClassMethodsHydrator());
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
        self::assertNotSame($categories[0], $cat1);
        self::assertNotSame($categories[1], $cat2);
    }

    /**
     * @group issue-6585
     * @group issue-6614
     */
    public function testAddingCollectionElementAfterBind(): void
    {
        $form = new Form();
        $form->setHydrator(new ObjectPropertyHydrator());

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
        self::assertTrue($form->isValid());
    }

    /**
     * @group issue-6585
     * @group issue-6614
     */
    public function testDoesNotCreateNewObjectsWhenUsingNestedCollections(): void
    {
        $addressesFieldset = new AddressFieldset();
        $addressesFieldset->setHydrator(new ClassMethodsHydrator());
        $addressesFieldset->remove('city');

        $form = new Form();
        $form->setHydrator(new ObjectPropertyHydrator());
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

        self::assertTrue($form->isValid());
        $phones = $customer->addresses[0]->getPhones();
        self::assertSame($phone, $phones[0]);
    }

    public function testDoNotCreateExtraFieldsetOnMultipleBind(): void
    {
        $form = new Form();
        $this->productFieldset->setHydrator(new ClassMethodsHydrator());
        $form->add($this->productFieldset);
        $form->setHydrator(new ObjectPropertyHydrator());

        $product            = new Product();
        $categoriesFieldset = [
            new Category(),
            new Category(),
        ];
        $product->setCategories($categoriesFieldset);

        $market          = new stdClass();
        $market->product = $product;

        $productFieldset = $form->get('product');
        self::assertInstanceOf(Fieldset::class, $productFieldset);
        $categoriesFieldset = $productFieldset->get('categories');
        self::assertInstanceOf(Fieldset::class, $categoriesFieldset);
        // this will pass the test
        $form->bind($market);
        self::assertSame(count($categoriesFieldset), iterator_count($categoriesFieldset->getIterator()));

        // this won't pass, but must
        $form->bind($market);
        self::assertSame(count($categoriesFieldset), iterator_count($categoriesFieldset->getIterator()));
    }

    public function testExtractDefaultIsEmptyArray(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        self::assertEquals([], $collection->extract());
    }

    public function testExtractThroughTargetElementHydrator(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        $this->prepareForExtract($collection);

        $expected = [
            'obj2' => ['field' => 'fieldOne'],
            'obj3' => ['field' => 'fieldTwo'],
        ];

        self::assertEquals($expected, $collection->extract());
    }

    public function testExtractMaintainsTargetElementObject(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        $this->prepareForExtract($collection);

        $targetElement = $collection->getTargetElement();
        self::assertInstanceOf(FieldsetInterface::class, $targetElement);
        $expected = $targetElement->getObject();

        $collection->extract();

        $test = $targetElement->getObject();

        self::assertSame($expected, $test);
    }

    public function testExtractThroughCustomHydrator(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        $this->prepareForExtract($collection);

        $mockHydrator = $this->createMock(HydratorInterface::class);
        $mockHydrator->expects($this->exactly(2))
            ->method('extract')
            ->willReturnCallback(static fn(object $object): array => ['value' => $object->field . '_foo']);

        $collection->setHydrator($mockHydrator);

        $expected = [
            'obj2' => ['value' => 'fieldOne_foo'],
            'obj3' => ['value' => 'fieldTwo_foo'],
        ];

        self::assertEquals($expected, $collection->extract());
    }

    public function testExtractFromTraversable(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);
        $this->prepareForExtract($collection);

        $traversable = new ArrayObject($collection->getObject());
        $collection->setObject($traversable);

        $expected = [
            'obj2' => ['field' => 'fieldOne'],
            'obj3' => ['field' => 'fieldTwo'],
        ];

        self::assertEquals($expected, $collection->extract());
    }

    public function testValidateData(): void
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

        self::assertTrue($myForm->isValid());
        self::assertEmpty($myForm->getMessages());
    }

    protected function prepareForExtract(Collection $collection): void
    {
        $targetElement = $collection->getTargetElement();
        self::assertInstanceOf(FieldsetInterface::class, $targetElement);

        $obj1 = new stdClass();

        $targetElement
            ->setHydrator(new ObjectPropertyHydrator())
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

    public function testCollectionCanBindObjectAndPopulateAndExtractNestedFieldsets(): void
    {
        $productFieldset = new ProductFieldset();
        $productFieldset->setHydrator(new ClassMethodsHydrator());

        $mainFieldset = new Fieldset();
        $mainFieldset->setObject(new stdClass());
        $mainFieldset->setHydrator(new ObjectPropertyHydrator());
        $mainFieldset->add($productFieldset);

        $form = new Form();
        $form->setHydrator(new ObjectPropertyHydrator());
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
        self::assertInstanceOf(Collection::class, $marketCollection);

        foreach ($marketCollection as $shopFieldset) {
            self::assertInstanceOf(Fieldset::class, $shopFieldset);
            self::assertInstanceOf(stdClass::class, $shopFieldset->getObject());

            // test for collection -> fieldset
            $productFieldset = $shopFieldset->get('product');
            self::assertInstanceOf(ProductFieldset::class, $productFieldset);
            self::assertInstanceOf(Product::class, $productFieldset->getObject());

            // test for collection -> fieldset -> fieldset
            $madeInCountry = $productFieldset->get('made_in_country');
            self::assertInstanceOf(CountryFieldset::class, $madeInCountry);
            self::assertInstanceOf(Country::class, $madeInCountry->getObject());

            // test for collection -> fieldset -> collection
            $productCategories = $productFieldset->get('categories');
            self::assertInstanceOf(Collection::class, $productCategories);

            // test for collection -> fieldset -> collection -> fieldset
            foreach ($productCategories as $category) {
                self::assertInstanceOf(CategoryFieldset::class, $category);
                self::assertInstanceOf(Category::class, $category->getObject());
            }
        }

        // test for correct extract and populate form values
        // test for collection -> fieldset -> field value
        foreach ($prices as $key => $price) {
            $field1 = $marketCollection->get((string) $key);
            self::assertInstanceOf(Fieldset::class, $field1);
            $field2 = $field1->get('product');
            self::assertInstanceOf(Fieldset::class, $field2);
            self::assertEquals(
                $price,
                $field2
                    ->get('price')
                    ->getValue()
            );
        }

        // test for collection -> fieldset -> fieldset ->field value
        foreach ($productCountries as $key => $countryName) {
            $field1 = $marketCollection->get((string) $key);
            self::assertInstanceOf(Fieldset::class, $field1);
            $field2 = $field1->get('product');
            self::assertInstanceOf(Fieldset::class, $field2);
            $field3 = $field2->get('made_in_country');
            self::assertInstanceOf(Fieldset::class, $field3);
            self::assertEquals(
                $countryName,
                $field3
                    ->get('name')
                    ->getValue()
            );
        }

        // test collection -> fieldset -> collection -> fieldset -> field value
        foreach ($categoryNames as $key => $categoryName) {
            $field1 = $marketCollection->get((string) $key);
            self::assertInstanceOf(Fieldset::class, $field1);
            $field2 = $field1->get('product');
            self::assertInstanceOf(Fieldset::class, $field2);
            $field3 = $field2->get('categories');
            self::assertInstanceOf(Fieldset::class, $field3);
            $field4 = $field3->get((string) 0);
            self::assertInstanceOf(Fieldset::class, $field4);
            self::assertEquals(
                $categoryName,
                $field4
                    ->get('name')
                    ->getValue()
            );
        }
    }

    public function testExtractFromTraversableImplementingToArrayThroughCollectionHydrator(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);

        // this test is using a hydrator set on the collection
        $collection->setHydrator(new ArraySerializableHydrator());

        $this->prepareForExtractWithCustomTraversable($collection);

        $expected = [
            ['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1'],
            ['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2'],
        ];

        self::assertEquals($expected, $collection->extract());
    }

    public function testExtractFromTraversableImplementingToArrayThroughTargetElementHydrator(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);

        // this test is using a hydrator set on the target element of the collection
        $targetElement = $collection->getTargetElement();
        self::assertInstanceOf(FieldsetInterface::class, $targetElement);
        $targetElement->setHydrator(new ArraySerializableHydrator());
        $obj1 = new ArrayModel();
        $targetElement->setObject($obj1);

        $this->prepareForExtractWithCustomTraversable($collection);

        $expected = [
            ['foo' => 'foo_value_1', 'bar' => 'bar_value_1', 'foobar' => 'foobar_value_1'],
            ['foo' => 'foo_value_2', 'bar' => 'bar_value_2', 'foobar' => 'foobar_value_2'],
        ];

        self::assertEquals($expected, $collection->extract());
    }

    protected function prepareForExtractWithCustomTraversable(Collection $collection): void
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

    public function testPopulateValuesWithFirstKeyGreaterThanZero(): void
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
        self::assertInstanceOf(Collection::class, $formCollection);

        $collection->populateValues($inputData);
        $formCollection->populateValues($inputData);

        self::assertCount(count($collection->getFieldsets()), $inputData);
        self::assertCount(count($formCollection->getFieldsets()), $inputData);
    }

    public function testCanRemoveAllElementsIfAllowRemoveIsTrue(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowRemove(true);
        $collection->setCount(0);

        // By default, $collection contains 2 elements
        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        self::assertCount(2, $collection->getElements());

        $collection->populateValues([]);
        self::assertCount(0, $collection->getElements());
    }

    public function testCanBindObjectMultipleNestedFieldsets(): void
    {
        $productFieldset = new ProductFieldset();
        $productFieldset->setHydrator(new ArraySerializableHydrator());
        $productFieldset->setObject(new Product());

        $nestedFieldset = new Fieldset('nested');
        $nestedFieldset->setHydrator(new ObjectPropertyHydrator());
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
        $mainFieldset->setHydrator(new ObjectPropertyHydrator());
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
        $form->setHydrator(new ObjectPropertyHydrator());
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
        $main = $form->get('main');
        self::assertInstanceOf(Fieldset::class, $main);
        self::assertCount(1, $main->getFieldsets());
        foreach ($main->getFieldsets() as $fieldset) {
            // ...which contains two stdClass objects (shops)
            self::assertCount(2, $fieldset->getFieldsets());
            foreach ($fieldset->getFieldsets() as $nestedfieldset) {
                // Each shop is represented by a single fieldset
                self::assertCount(1, $nestedfieldset->getFieldsets());
                foreach ($nestedfieldset->getFieldsets() as $productfieldset) {
                    // Each shop fieldset contain a collection with two products in it
                    self::assertCount(2, $productfieldset->getFieldsets());
                    foreach ($productfieldset->getFieldsets() as $product) {
                        self::assertInstanceOf(Product::class, $product->getObject());
                    }
                }
            }
        }
    }

    public function testNestedCollections(): void
    {
        // @see https://github.com/zendframework/zf2/issues/5640
        $addressesFieldeset = new AddressFieldset();
        $addressesFieldeset->setHydrator(new ClassMethodsHydrator());

        $form = new Form();
        $form->setHydrator(new ObjectPropertyHydrator());
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
        $addresses = $form->get('addresses');
        self::assertInstanceOf(Collection::class, $addresses);
        foreach ($addresses->getFieldsets() as $fieldset) {
            self::assertInstanceOf(Address::class, $fieldset->getObject());
            foreach ($fieldset->getFieldsets() as $childFieldsetName => $childFieldset) {
                switch ($childFieldsetName) {
                    case 'city':
                        self::assertInstanceOf(City::class, $childFieldset->getObject());
                        break;
                    case 'phones':
                        foreach ($childFieldset->getFieldsets() as $phoneFieldset) {
                            self::assertInstanceOf(
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
        foreach ($addresses as $addresses) {
            self::assertEquals($data[$index]['street'], $addresses->get('street')->getValue());
            //assuming data has just 1 phone entry
            foreach ($addresses->get('phones') as $phone) {
                self::assertEquals($data[$index]['number'], $phone->get('number')->getValue());
            }
            $index++;
        }
    }

    public function testSetDataOnFormPopulatesCollection(): void
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

        $namesCollection = $form->get('names');
        self::assertInstanceOf(Collection::class, $namesCollection);

        self::assertCount(count($names), $namesCollection);

        $i = 0;
        foreach ($namesCollection as $field) {
            self::assertEquals($names[$i], $field->getValue());
            $i++;
        }
    }

    public function testSettingSomeDataButNoneForCollectionReturnsSpecifiedNumberOfElementsAfterPrepare(): void
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

        $namesCollection = $form->get('names');
        self::assertInstanceOf(Collection::class, $namesCollection);

        self::assertCount(0, $namesCollection);

        $form->prepare();

        self::assertCount(2, $namesCollection);
    }

    public function testMininumLenghtIsMaintanedWhenSettingASmallerCollection(): void
    {
        $arrayCollection = [
            new Element\Color(),
            new Element\Color(),
        ];

        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setCount(3);
        $collection->setObject($arrayCollection);
        self::assertEquals(3, $collection->getCount());
    }

    /**
     * @group issue-6263
     * @group issue-6518
     */
    public function testCollectionProperlyHandlesAddingObjectsOfTypeElementInterface(): void
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
        self::assertTrue($form->isValid());

        $result = $form->getData();
        self::assertInstanceOf(ArrayAccess::class, $result);
        self::assertArrayHasKey('text', $result);
        self::assertIsArray($result['text']);
        self::assertArrayHasKey(0, $result['text']);
        self::assertEquals('Foo', $result['text'][0]);
        self::assertArrayHasKey(1, $result['text']);
        self::assertEquals('Bar', $result['text'][1]);
    }

    /**
     * Unit test to ensure behavior of extract() method is unaffected by refactor
     *
     * @group issue-6263
     * @group issue-6518
     */
    public function testCollectionShouldSilentlyIgnorePopulatingFieldsetWithDisallowedObject(): void
    {
        $mainFieldset = new Fieldset();
        $mainFieldset->add(new Element\Text('test'));
        $mainFieldset->setObject(new ArrayObject());
        $mainFieldset->setHydrator(new ObjectPropertyHydrator());

        $form = new Form();
        $form->setObject(new stdClass());
        $form->setHydrator(new ObjectPropertyHydrator());
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
        self::assertTrue($form->isValid());

        $result = $form->getData();
        self::assertInstanceOf(stdClass::class, $result);
        self::assertTrue(isset($result->collection));
        self::assertIsArray($result->collection);
        self::assertCount(1, $result->collection);
        self::assertInstanceOf(ArrayObject::class, $result->collection[0]);
        self::assertArrayHasKey('test', $result->collection[0]);
        self::assertEquals('bar', $result->collection[0]['test']);
    }

    /**
     * @group issue-6263
     * @group issue-6298
     */
    public function testCanHydrateObject(): void
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
        self::assertTrue($form->isValid());
        self::assertIsArray($object['colors']);
        self::assertCount(1, $object['colors']);
    }

    public function testCanRemoveMultipleElements(): void
    {
        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowRemove(true);
        $collection->setCount(0);

        $data   = [];
        $data[] = 'blue';
        $data[] = 'green';
        $data[] = 'red';

        $collection->populateValues($data);

        $collection->populateValues(['colors' => ['0' => 'blue']]);
        self::assertCount(1, $collection->getElements());
    }

    public function testGetErrorMessagesForInvalidCollectionElements(): void
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

        self::assertEquals(
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
    public function testNullTargetElementShouldResultInEmptyData(): void
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
        self::assertEquals(
            [
                'fieldsets' => [],
            ],
            $form->getData()
        );
    }

    public function testPopulateValuesTraversable(): void
    {
        $data = new CustomTraversable(['blue', 'green']);

        $collection = $this->form->get('colors');
        self::assertInstanceOf(Collection::class, $collection);
        $collection->setAllowRemove(false);
        $collection->populateValues($data);

        self::assertCount(2, $collection->getElements());
    }

    public function testSetObjectTraversable(): void
    {
        $collection = $this->form->get('fieldsets');
        self::assertInstanceOf(Collection::class, $collection);

        // this test is using a hydrator set on the target element of the collection
        $targetElement = $collection->getTargetElement();
        self::assertInstanceOf(FieldsetInterface::class, $targetElement);
        $targetElement->setHydrator(new ArraySerializableHydrator());
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

        self::assertSame($expected, $collection->extract());
        self::assertSame([$obj2, $obj3], $collection->getObject());
    }
}
