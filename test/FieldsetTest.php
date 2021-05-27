<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Hydrator;
use Laminas\InputFilter\InputFilter;
use PHPUnit\Framework\TestCase;
use stdClass;

use function class_exists;

class FieldsetTest extends TestCase
{
    /** @var Fieldset */
    private $fieldset;

    protected function setUp(): void
    {
        $this->fieldset = new Fieldset();
    }

    public function populateFieldset(): void
    {
        $this->fieldset->add(new Element('foo'));
        $this->fieldset->add(new Element('bar'));
        $this->fieldset->add(new Element('baz'));

        $subFieldset = new Fieldset('foobar');
        $subFieldset->add(new Element('foo'));
        $subFieldset->add(new Element('bar'));
        $subFieldset->add(new Element('baz'));
        $this->fieldset->add($subFieldset);

        $subFieldset = new Fieldset('barbaz');
        $subFieldset->add(new Element('foo'));
        $subFieldset->add(new Element('bar'));
        $subFieldset->add(new Element('baz'));
        $this->fieldset->add($subFieldset);
    }

    public function getMessages(): array
    {
        return [
            'foo'    => [
                'Foo message 1',
            ],
            'bar'    => [
                'Bar message 1',
                'Bar message 2',
            ],
            'baz'    => [
                'Baz message 1',
            ],
            'foobar' => [
                'foo' => [
                    'Foo message 1',
                ],
                'bar' => [
                    'Bar message 1',
                    'Bar message 2',
                ],
                'baz' => [
                    'Baz message 1',
                ],
            ],
            'barbaz' => [
                'foo' => [
                    'Foo message 1',
                ],
                'bar' => [
                    'Bar message 1',
                    'Bar message 2',
                ],
                'baz' => [
                    'Baz message 1',
                ],
            ],
        ];
    }

    public function testExtractOnAnEmptyRelationship(): void
    {
        $form = new TestAsset\FormCollection();
        $form->populateValues(['fieldsets' => []]);

        $this->addToAssertionCount(1);
    }

    public function testExtractOnAnEmptyTraversable(): void
    {
        $form = new TestAsset\FormCollection();
        $form->populateValues(new ArrayObject(['fieldsets' => new ArrayObject()]));

        $this->addToAssertionCount(1);
    }

    public function testTraversableAcceptedValueForFieldset(): void
    {
        $subValue    = new ArrayObject(['field' => 'value']);
        $subFieldset = new TestAsset\ValueStoringFieldset('subFieldset');
        $this->fieldset->add($subFieldset);
        $this->fieldset->populateValues(['subFieldset' => $subValue]);
        $this->assertEquals($subValue, $subFieldset->getStoredValue());
    }

    public function testFieldsetIsEmptyByDefault(): void
    {
        $this->assertCount(0, $this->fieldset);
    }

    public function testCanAddElementsToFieldset(): void
    {
        $this->fieldset->add(new Element('foo'));
        $this->assertCount(1, $this->fieldset);
    }

    public function testCanSetCustomOptionFromConstructor(): void
    {
        $fieldset = new Fieldset('foo', [
            'custom' => 'option',
        ]);
        $options  = $fieldset->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testAddWithInvalidElementRaisesException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fieldset->add(null);
    }

    public function testCanGrabElementByNameWhenNotProvidedWithAlias(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $this->assertSame($element, $this->fieldset->get('foo'));
    }

    public function testElementMayBeRetrievedByAliasProvidedWhenAdded(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element, ['name' => 'bar']);
        $this->assertSame($element, $this->fieldset->get('bar'));
    }

    public function testElementNameIsChangedToAliasWhenAdded(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element, ['name' => 'bar']);
        $this->assertEquals('bar', $element->getName());
    }

    public function testCannotRetrieveElementByItsNameWhenProvidingAnAliasDuringAddition(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element, ['name' => 'bar']);
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testAddingAnElementWithNoNameOrAliasWillRaiseException(): void
    {
        $element = new Element();
        $this->expectException(InvalidArgumentException::class);
        $this->fieldset->add($element);
    }

    public function testCanAddFieldsetsToFieldset(): void
    {
        $fieldset = new Fieldset('foo');
        $this->fieldset->add($fieldset);
        $this->assertCount(1, $this->fieldset);
    }

    public function testCanRemoveElementsByName(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->fieldset->remove('foo');
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testCanRemoveFieldsetsByName(): void
    {
        $fieldset = new Fieldset('foo');
        $this->fieldset->add($fieldset);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->fieldset->remove('foo');
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testCanRemoveElementsByWrongName(): void
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $element2 = new Element('bar');
        $this->fieldset->add($element2);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertTrue($this->fieldset->has('bar'));

        // remove wrong element, bar still available
        $this->fieldset->remove('bars');
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertTrue($this->fieldset->has('bar'));

        $this->fieldset->remove('bar');
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertFalse($this->fieldset->has('bar'));
    }

    public function testCanRetrieveAllAttachedElementsSeparateFromFieldsetsAtOnce(): void
    {
        $this->populateFieldset();
        $elements = $this->fieldset->getElements();
        $this->assertCount(3, $elements);
        foreach (['foo', 'bar', 'baz'] as $name) {
            $this->assertTrue(isset($elements[$name]));
            $element = $this->fieldset->get($name);
            $this->assertSame($element, $elements[$name]);
        }
    }

    public function testCanRetrieveAllAttachedFieldsetsSeparateFromElementsAtOnce(): void
    {
        $this->populateFieldset();
        $fieldsets = $this->fieldset->getFieldsets();
        $this->assertCount(2, $fieldsets);
        foreach (['foobar', 'barbaz'] as $name) {
            $this->assertTrue(isset($fieldsets[$name]));
            $fieldset = $this->fieldset->get($name);
            $this->assertSame($fieldset, $fieldsets[$name]);
        }
    }

    public function testCanSetAndRetrieveErrorMessagesForAllElementsAndFieldsets(): void
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);
        $test = $this->fieldset->getMessages();
        $this->assertEquals($messages, $test);
    }

    public function testOnlyElementsWithErrorsInMessages(): void
    {
        $fieldset = new TestAsset\FieldsetWithInputFilter('set');
        $fieldset->add(new Element('foo'));
        $fieldset->add(new Element('bar'));

        $form = new Form();
        $form->add($fieldset);
        $form->setInputFilter(new InputFilter());
        $form->setData([]);
        $form->isValid();

        $messages = $form->getMessages();
        $this->assertArrayHasKey('foo', $messages['set']);
        $this->assertArrayNotHasKey('bar', $messages['set']);
    }

    public function testCanRetrieveMessagesForSingleElementsAfterMessagesHaveBeenSet(): void
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);

        $test = $this->fieldset->getMessages('bar');
        $this->assertEquals($messages['bar'], $test);
    }

    public function testCanRetrieveMessagesForSingleFieldsetsAfterMessagesHaveBeenSet(): void
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);

        $test = $this->fieldset->getMessages('barbaz');
        $this->assertEquals($messages['barbaz'], $test);
    }

    public function testGetMessagesWithInvalidElementRaisesException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fieldset->getMessages('foo');
    }

    public function testCountGivesCountOfAttachedElementsAndFieldsets(): void
    {
        $this->populateFieldset();
        $this->assertCount(5, $this->fieldset);
    }

    public function testCanIterateOverElementsAndFieldsetsInOrderAttached(): void
    {
        $this->populateFieldset();
        $expected = ['foo', 'bar', 'baz', 'foobar', 'barbaz'];
        $test     = [];
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityProvidedWhenAttaching(): void
    {
        $this->fieldset->add(new Element('foo'), ['priority' => 10]);
        $this->fieldset->add(new Element('bar'), ['priority' => 20]);
        $this->fieldset->add(new Element('baz'), ['priority' => -10]);
        $this->fieldset->add(new Fieldset('barbaz'), ['priority' => 30]);

        $expected = ['barbaz', 'bar', 'foo', 'baz'];
        $test     = [];
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityProvidedWhenSetLater(): void
    {
        $this->fieldset->add(new Element('foo'), ['priority' => 10]);
        $this->fieldset->add(new Element('bar'), ['priority' => 20]);
        $this->fieldset->add(new Element('baz'), ['priority' => -10]);
        $this->fieldset->add(new Fieldset('barbaz'), ['priority' => 30]);
        $this->fieldset->setPriority('baz', 99);

        $expected = ['baz', 'barbaz', 'bar', 'foo'];
        $test     = [];
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityWhenCloned(): void
    {
        $this->fieldset->add(new Element('foo'), ['priority' => 10]);
        $this->fieldset->add(new Element('bar'), ['priority' => 20]);
        $this->fieldset->add(new Element('baz'), ['priority' => -10]);
        $this->fieldset->add(new Fieldset('barbaz'), ['priority' => 30]);

        $expected = ['barbaz', 'bar', 'foo', 'baz'];

        $testOrig  = [];
        $testClone = [];

        $fieldsetClone = clone $this->fieldset;

        foreach ($this->fieldset as $element) {
            $testOrig[] = $element->getName();
        }

        foreach ($fieldsetClone as $element) {
            $testClone[] = $element->getName();
        }

        $this->assertEquals($expected, $testClone);
        $this->assertEquals($testOrig, $testClone);
    }

    public function testCloneDeepClonesElementsAndObject(): void
    {
        $this->fieldset->add(new Element('foo'));
        $this->fieldset->add(new Element('bar'));
        $this->fieldset->setObject(new stdClass());

        $fieldsetClone = clone $this->fieldset;

        $this->assertNotSame($this->fieldset->get('foo'), $fieldsetClone->get('foo'));
        $this->assertNotSame($this->fieldset->get('bar'), $fieldsetClone->get('bar'));
        $this->assertNotSame($this->fieldset->getObject(), $fieldsetClone->getObject());
    }

    public function testSubFieldsetsBindObject(): void
    {
        $form     = new Form();
        $fieldset = new Fieldset('foobar');
        $form->add($fieldset);
        $value = new ArrayObject([
            'bar'    => 'abc',
            'foobar' => new ArrayObject([
                'foo' => 'abc',
            ]),
        ]);
        $form->bind($value);
        $this->assertSame($fieldset, $form->get('foobar'));
    }

    public function testBindEmptyValue(): void
    {
        $value = new ArrayObject([
            'foo' => 'abc',
            'bar' => 'def',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add(['name' => 'foo', 'required' => false]);
        $inputFilter->add(['name' => 'bar', 'required' => false]);

        $form = new Form();
        $form->add(new Element('foo'));
        $form->add(new Element('bar'));
        $form->setInputFilter($inputFilter);
        $form->bind($value);
        $form->setData([
            'foo' => '',
            'bar' => 'ghi',
        ]);
        $form->isValid();

        $this->assertSame('', $value['foo']);
        $this->assertSame('ghi', $value['bar']);
    }

    public function testFieldsetExposesFluentInterface(): void
    {
        $fieldset = $this->fieldset->add(new Element('foo'));
        $this->assertSame($this->fieldset, $fieldset);
        $fieldset = $this->fieldset->remove('foo');
        $this->assertSame($this->fieldset, $fieldset);
    }

    public function testSetOptions(): void
    {
        $this->fieldset->setOptions([
            'foo' => 'bar',
        ]);
        $option = $this->fieldset->getOption('foo');

        $this->assertEquals('bar', $option);
    }

    public function testSetOptionsUseAsBaseFieldset(): void
    {
        $this->fieldset->setOptions([
            'use_as_base_fieldset' => true,
        ]);
        $this->assertTrue($this->fieldset->getOption('use_as_base_fieldset'));
    }

    public function testSetOptionAllowedObjectBindingClass(): void
    {
        $this->fieldset->setOptions([
            'allowed_object_binding_class' => 'bar',
        ]);
        $option = $this->fieldset->getOption('allowed_object_binding_class');

        $this->assertEquals('bar', $option);
    }

    public function testShouldThrowExceptionWhenGetInvalidElement(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fieldset->get('doesnt_exist');
    }

    public function testBindValuesHasNoName(): void
    {
        $bindValues = $this->fieldset->bindValues(['foo']);
        $this->assertNull($bindValues);
    }

    public function testBindValuesSkipDisabled(): void
    {
        $object               = new stdClass();
        $object->disabled     = 'notModified';
        $object->not_disabled = 'notModified';

        $textInput     = new Element\Text('not_disabled');
        $disabledInput = new Element\Text('disabled');
        $disabledInput->setAttribute('disabled', 'disabled');

        $form = new Form();
        $form->add($textInput);
        $form->add($disabledInput);

        $form->setObject($object);
        $form->setHydrator(
            class_exists(Hydrator\ObjectPropertyHydrator::class)
                ? new Hydrator\ObjectPropertyHydrator()
                : new Hydrator\ObjectProperty()
        );
        $form->bindValues(['not_disabled' => 'modified', 'disabled' => 'modified']);

        $this->assertEquals('modified', $object->not_disabled);
        $this->assertEquals('notModified', $object->disabled);
    }

    /**
     * @group issue-7109
     */
    public function testBindValuesDoesNotSkipElementsWithFalsyDisabledValues(): void
    {
        $object               = new stdClass();
        $object->disabled     = 'notModified';
        $object->not_disabled = 'notModified';

        $textInput     = new Element\Text('not_disabled');
        $disabledInput = new Element\Text('disabled');
        $disabledInput->setAttribute('disabled', '');

        $form = new Form();
        $form->add($textInput);
        $form->add($disabledInput);

        $form->setObject($object);
        $form->setHydrator(
            class_exists(Hydrator\ObjectPropertyHydrator::class)
                ? new Hydrator\ObjectPropertyHydrator()
                : new Hydrator\ObjectProperty()
        );
        $form->bindValues(['not_disabled' => 'modified', 'disabled' => 'modified']);

        $this->assertEquals('modified', $object->not_disabled);
        $this->assertEquals('modified', $object->disabled);
    }

    public function testSetObjectWithStringRaisesException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fieldset->setObject('foo');
    }

    public function testShouldValidateAllowObjectBindingByClassname(): void
    {
        $object = new stdClass();
        $this->fieldset->setAllowedObjectBindingClass('stdClass');
        $allowed = $this->fieldset->allowObjectBinding($object);

        $this->assertTrue($allowed);
    }

    public function testShouldValidateAllowObjectBindingByObject(): void
    {
        $object = new stdClass();
        $this->fieldset->setObject($object);
        $allowed = $this->fieldset->allowObjectBinding($object);

        $this->assertTrue($allowed);
    }

    /**
     * @group issue-6585
     * @group issue-6614
     */
    public function testBindValuesPreservesNewValueAfterValidation(): void
    {
        $form = new Form();
        $form->add(new Element('foo'));
        $form->setHydrator(
            class_exists(Hydrator\ObjectPropertyHydrator::class)
                ? new Hydrator\ObjectPropertyHydrator()
                : new Hydrator\ObjectProperty()
        );

        $object      = new stdClass();
        $object->foo = 'Initial value';
        $form->bind($object);

        $form->setData([
            'foo' => 'New value',
        ]);

        $this->assertSame('New value', $form->get('foo')->getValue());

        $form->isValid();

        $this->assertSame('New value', $form->get('foo')->getValue());
    }

    /**
     * Error test for https://github.com/zendframework/zend-form/issues/135
     */
    public function testSetAndGetErrorMessagesForNonExistentElements(): void
    {
        $messages = [
            'foo' => [
                'foo_message_key' => 'foo_message_val',
            ],
            'bar' => [
                'bar_message_key' => 'bar_message_val',
            ],
        ];

        $fieldset = new Fieldset();
        $fieldset->setMessages($messages);

        $this->assertEquals($messages, $fieldset->getMessages());
    }

    public function testSetNullValueWhenArrayProvided(): void
    {
        $subValue   = 'sub-element-value';
        $subElement = new Element('subElement');
        $this->fieldset->add($subElement);
        $this->fieldset->populateValues(['subElement' => $subValue]);
        $this->assertSame($subValue, $subElement->getValue());

        $this->fieldset->populateValues(['subElement' => null]);
        $this->assertNull($subElement->getValue());
    }

    public function testSetNullValueWhenTraversableProvided(): void
    {
        $subValue   = 'sub-element-value';
        $subElement = new Element('subElement');
        $this->fieldset->add($subElement);
        $this->fieldset->populateValues(new TestAsset\CustomTraversable(['subElement' => $subValue]));
        $this->assertSame($subValue, $subElement->getValue());

        $this->fieldset->populateValues(new TestAsset\CustomTraversable(['subElement' => null]));
        $this->assertNull($subElement->getValue());
    }
}
