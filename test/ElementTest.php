<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form;

use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage UnitTest
 */
class ElementTest extends TestCase
{
    public function testAttributesAreEmptyByDefault()
    {
        $element = new Element();
        $this->assertEquals(array(), $element->getAttributes());
    }

    public function testCanAddAttributesSingly()
    {
        $element = new Element();
        $element->setAttribute('data-foo', 'bar');
        $this->assertEquals('bar', $element->getAttribute('data-foo'));
    }

    public function testCanAddManyAttributesAtOnce()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $this->assertEquals($attributes, $element->getAttributes());
    }

    public function testAddingAttributesMerges()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $attributesExtra = array(
            'data-foo' => 'baz',
            'width'    => 20,
        );
        $element->setAttributes($attributes);
        $element->setAttributes($attributesExtra);
        $expected = array_merge($attributes, $attributesExtra);
        $this->assertEquals($expected, $element->getAttributes());
    }

    public function testCanClearAllAttributes()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $element->clearAttributes();
        $this->assertEquals(array(), $element->getAttributes());
    }

    public function testSettingNameSetsNameAttribute()
    {
        $element = new Element();
        $element->setName('foo');
        $this->assertEquals('foo', $element->getAttribute('name'));
    }

    public function testSettingNameAttributeAllowsRetrievingName()
    {
        $element = new Element();
        $element->setAttribute('name', 'foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanPassNameToConstructor()
    {
        $element = new Element('foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanSetCustomOptionFromConstructor()
    {
        $element = new Element('foo', array(
            'custom' => 'option'
        ));
        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanSetCustomOptionFromMethod()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'custom' => 'option'
        ));

        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanRetrieveSpecificOption()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'custom' => 'option'
        ));
        $option = $element->getOption('custom');
        $this->assertEquals('option', $option);
    }
}
