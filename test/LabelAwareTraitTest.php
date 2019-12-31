<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Form;

use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.4
 * @group      Laminas_Form
 */
class LabelAwareTraitTest extends TestCase
{

    public function testSetLabelAttributes()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $this->assertAttributeEquals(null, 'labelAttributes', $object);

        $labelAttributes = [
            'test',
            'test2',
        ];

        $object->setLabelAttributes($labelAttributes);

        $this->assertAttributeEquals($labelAttributes, 'labelAttributes', $object);
    }

    public function testGetEmptyLabelAttributes()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelAttributes = $object->getLabelAttributes();

        $this->assertEquals(null, $labelAttributes);
    }

    public function testGetLabelAttributes()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelAttributes = [
            'test',
            'test2',
        ];

        $object->setLabelAttributes($labelAttributes);

        $getLabelAttributes = $object->getLabelAttributes();

        $this->assertEquals($labelAttributes, $getLabelAttributes);
    }

    public function testSetEmptyLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = [];

        $object->setLabelOptions($labelOptions);

        $this->assertEquals($labelOptions, []);
    }

    public function testSetLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = [
            'foo' => 'bar',
            'foo2' => 'bar2',
        ];


        $object->setLabelOptions($labelOptions);

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($retrievedLabelOptions, $labelOptions);
    }

    public function testGetEmptyLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = $object->getLabelOptions();

        $this->assertEquals($labelOptions, []);
    }

    public function testGetLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = [
            'foo' => 'bar',
            'foo2' => 'bar',
        ];

        $object->setLabelOptions($labelOptions);

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($labelOptions, $retrievedLabelOptions);
    }

    public function testClearLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = [
            'foo' => 'bar',
            'foo2' => 'bar',
        ];

        $object->setLabelOptions($labelOptions);

        $object->clearLabelOptions();

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals([], $retrievedLabelOptions);
    }

    public function testRemoveLabelOptions()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $labelOptions = [
            'foo' => 'bar',
            'foo2' => 'bar2',
        ];

        $object->setLabelOptions($labelOptions);

        $toRemoveLabelOptions = [
            'foo',
        ];

        $object->removeLabelOptions($toRemoveLabelOptions);

        $expectedLabelOptions = [
            'foo2' => 'bar2',
        ];

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($expectedLabelOptions, $retrievedLabelOptions);
    }

    public function testSetLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $object->setLabelOption('foo', 'bar');

        $expectedLabelOptions = [
            'foo' => 'bar',
        ];

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($expectedLabelOptions, $retrievedLabelOptions);
    }

    public function testGetInvalidLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $invalidOption = 'foo';

        $retrievedOption = $object->getLabelOption($invalidOption);

        $this->assertEquals(null, $retrievedOption);
    }

    public function testGetLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $option = 'foo';
        $value = 'bar';

        $object->setLabelOption($option, $value);

        $retrievedValue = $object->getLabelOption($option);

        $this->assertEquals($retrievedValue, $value);
    }

    public function testRemoveLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $option = 'foo';
        $value = 'bar';

        $object->setLabelOption($option, $value);

        $object->removeLabelOption($option);

        $retrievedValue = $object->getLabelOption($option);

        $this->assertEquals(null, $retrievedValue);
    }

    public function testHasValidLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $option = 'foo';
        $value = 'bar';

        $object->setLabelOption($option, $value);

        $hasLabelOptionResult = $object->hasLabelOption($option);
        $this->assertTrue($hasLabelOptionResult);
    }

    public function testHasInvalidLabelOption()
    {
        $object = $this->getObjectForTrait('\Laminas\Form\LabelAwareTrait');

        $option = 'foo';

        $hasLabelOptionResult = $object->hasLabelOption($option);
        $this->assertFalse($hasLabelOptionResult);
    }
}
