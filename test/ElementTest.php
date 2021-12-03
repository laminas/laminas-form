<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use ArrayObject;
use Laminas\Form\Element;
use Laminas\Form\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function array_merge;

final class ElementTest extends TestCase
{
    public function testAttributesAreEmptyByDefault(): void
    {
        $element = new Element();
        $this->assertEquals([], $element->getAttributes());
    }

    public function testLabelAttributesAreEmptyByDefault(): void
    {
        $element = new Element();
        $this->assertEquals([], $element->getLabelAttributes());
    }

    public function testCanAddAttributesSingly(): void
    {
        $element = new Element();
        $element->setAttribute('data-foo', 'bar');
        $this->assertEquals('bar', $element->getAttribute('data-foo'));
    }

    public function testCanAddManyAttributesAtOnce(): void
    {
        $element    = new Element();
        $attributes = [
            'type'               => 'text',
            'class'              => 'text-element',
            'data-foo'           => 'bar',
            'x-autocompletetype' => 'email',
        ];
        $element->setAttributes($attributes);
        $this->assertEquals($attributes, $element->getAttributes());
    }

    public function testAddingAttributesMerges(): void
    {
        $element         = new Element();
        $attributes      = [
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        ];
        $attributesExtra = [
            'data-foo' => 'baz',
            'width'    => 20,
        ];
        $element->setAttributes($attributes);
        $element->setAttributes($attributesExtra);
        $expected = array_merge($attributes, $attributesExtra);
        $this->assertEquals($expected, $element->getAttributes());
    }

    public function testCanClearAllAttributes(): void
    {
        $element    = new Element();
        $attributes = [
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        ];
        $element->setAttributes($attributes);
        $element->clearAttributes();
        $this->assertEquals([], $element->getAttributes());
    }

    public function testCanRemoveSingleAttribute(): void
    {
        $element    = new Element();
        $attributes = [
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        ];
        $element->setAttributes($attributes);
        $element->removeAttribute('type');
        $this->assertFalse($element->hasAttribute('type'));
    }

    public function testCanRemoveMultipleAttributes(): void
    {
        $element    = new Element();
        $attributes = [
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        ];
        $element->setAttributes($attributes);
        $element->removeAttributes(['type', 'class']);
        $this->assertFalse($element->hasAttribute('type'));
        $this->assertFalse($element->hasAttribute('class'));
    }

    public function testSettingNameSetsNameAttribute(): void
    {
        $element = new Element();
        $element->setName('foo');
        $this->assertEquals('foo', $element->getAttribute('name'));
    }

    public function testSettingNameAttributeAllowsRetrievingName(): void
    {
        $element = new Element();
        $element->setAttribute('name', 'foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanPassNameToConstructor(): void
    {
        $element = new Element('foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanSetCustomOptionFromConstructor(): void
    {
        $element = new Element('foo', [
            'custom' => 'option',
        ]);
        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanSetCustomOptionFromMethod(): void
    {
        $element = new Element('foo');
        $element->setOptions([
            'custom' => 'option',
        ]);

        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanRetrieveSpecificOption(): void
    {
        $element = new Element('foo');
        $element->setOptions([
            'custom' => 'option',
        ]);
        $option = $element->getOption('custom');
        $this->assertEquals('option', $option);
    }

    public function testSpecificOptionsSetLabelAttributes(): void
    {
        $element = new Element('foo');
        $element->setOptions([
            'label'            => 'foo',
            'label_attributes' => ['bar' => 'baz'],
        ]);
        $option = $element->getOption('label_attributes');
        $this->assertEquals(['bar' => 'baz'], $option);
    }

    public function testLabelOptionsAccessors(): void
    {
        $element = new Element('foo');
        $element->setOptions([
            'label_options' => ['moar' => 'foo'],
        ]);

        $labelOptions = $element->getLabelOptions();
        $this->assertEquals(['moar' => 'foo'], $labelOptions);
    }

    public function testCanSetSingleOptionForLabel(): void
    {
        $element = new Element('foo');
        $element->setOption('label', 'foo');
        $option = $element->getOption('label');
        $this->assertEquals('foo', $option);
    }

    public function testSetOptionsIsTraversable(): void
    {
        $element = new Element('foo');
        $element->setOptions(new ArrayObject(['foo' => 'bar']));
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals(['foo' => 'bar'], $element->getOptions());
    }

    public function testGetOption(): void
    {
        $element = new Element('foo');
        $this->assertNull($element->getOption('foo'));
    }

    public function testLabelOptionsAreEmptyByDefault(): void
    {
        $element = new Element();
        $this->assertEquals([], $element->getLabelOptions());
    }

    public function testLabelOptionsCanBeSetViaOptionsArray(): void
    {
        $element = new Element('foo');
        $element->setOptions([
            'label_options' => ['moar' => 'foo'],
        ]);

        $this->assertEquals('foo', $element->getLabelOption('moar'));
    }

    public function testCanAddLabelOptionSingly(): void
    {
        $element = new Element();
        $element->setLabelOption('foo', 'bar');
        $this->assertEquals('bar', $element->getLabelOption('foo'));
    }

    public function testCanAddManyLabelOptionsAtOnce(): void
    {
        $element = new Element();
        $options = [
            'foo'  => 'bar',
            'foo2' => 'baz',
        ];
        $element->setLabelOptions($options);

        // check each expected key individually
        foreach ($options as $k => $v) {
            $this->assertEquals($v, $element->getLabelOption($k));
        }
    }

    public function testSettingLabelOptionsMerges(): void
    {
        $element      = new Element();
        $options      = [
            'foo'  => 'bar',
            'foo2' => 'baz',
        ];
        $optionsExtra = [
            'foo3' => 'bar2',
            'foo2' => 'baz2',
        ];
        $element->setLabelOptions($options);
        $element->setLabelOptions($optionsExtra);
        $expected = array_merge($options, $optionsExtra);

        // check each expected key individually
        foreach ($expected as $k => $v) {
            $this->assertEquals($v, $element->getLabelOption($k));
        }
    }

    public function testCanClearAllLabelOptions(): void
    {
        $element = new Element();
        $options = [
            'foo'  => 'bar',
            'foo2' => 'baz',
        ];
        $element->setLabelOptions($options);
        $element->clearLabelOptions();
        $this->assertEquals([], $element->getLabelOptions());
    }

    public function testCanRemoveSingleLabelOption(): void
    {
        $element = new Element();
        $options = [
            'foo'  => 'bar',
            'foo2' => 'baz',
        ];
        $element->setLabelOptions($options);
        $element->removeLabelOption('foo2');
        $this->assertFalse($element->hasLabelOption('foo2'));
    }

    public function testCanRemoveMultipleLabelOptions(): void
    {
        $element = new Element();
        $options = [
            'foo'  => 'bar',
            'foo2' => 'baz',
            'foo3' => 'bar2',
        ];
        $element->setLabelOptions($options);
        $element->removeLabelOptions(['foo', 'foo2']);
        $this->assertFalse($element->hasLabelOption('foo'));
        $this->assertFalse($element->hasLabelOption('foo2'));
        $this->assertTrue($element->hasLabelOption('foo3'));
    }

    public function testCanAddMultipleAriaAttributes(): void
    {
        $element    = new Element();
        $attributes = [
            'type'             => 'text',
            'aria-label'       => 'alb',
            'aria-describedby' => 'adb',
            'aria-orientation' => 'vertical',
        ];
        $element->setAttributes($attributes);
        $this->assertTrue($element->hasAttribute('aria-describedby'));
        $this->assertTrue($element->hasAttribute('aria-label'));
        $this->assertTrue($element->hasAttribute('aria-orientation'));
    }

    public function testCanRemoveMultipleAriaAttributes(): void
    {
        $element    = new Element();
        $attributes = [
            'type'             => 'text',
            'aria-label'       => 'alb',
            'aria-describedby' => 'adb',
            'aria-orientation' => 'vertical',
        ];
        $element->setAttributes($attributes);
        $element->removeAttributes(['aria-label', 'aria-describedby', 'aria-orientation']);
        $this->assertFalse($element->hasAttribute('aria-describedby'));
        $this->assertFalse($element->hasAttribute('aria-label'));
        $this->assertFalse($element->hasAttribute('aria-orientation'));
    }

    public function testHasValueGettingSetInSetValueMethod(): void
    {
        $element = new Element();
        $element->setValue('some value');
        $this->assertTrue($element->hasValue());
    }

    public function testHasValueIsFalseAtTheTimeOfCreation(): void
    {
        $element = new Element();
        $this->assertFalse($element->hasValue());
    }

    public function testConstructMustRejectNonStringNameToBeConsistentWithRetrievalMethods(): void
    {
        new Element('1');

        $this->expectException(InvalidArgumentException::class);

        new Element(1);
    }
}
