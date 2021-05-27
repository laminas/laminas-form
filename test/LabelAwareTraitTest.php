<?php

declare(strict_types=1);

namespace LaminasTest\Form;

use Laminas\Form\LabelAwareTrait;
use LaminasTest\Form\TestAsset\CustomTraversable;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.4
 * @group      Laminas_Form
 */
class LabelAwareTraitTest extends TestCase
{
    public function testSetLabelAttributes(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $this->assertEmpty($object->getLabelAttributes());

        $labelAttributes = [
            'test',
            'test2',
        ];

        $object->setLabelAttributes($labelAttributes);

        $this->assertSame($labelAttributes, $object->getLabelAttributes());
    }

    public function testGetEmptyLabelAttributes(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $this->assertEmpty($object->getLabelAttributes());
    }

    public function testGetLabelAttributes(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelAttributes = [
            'test',
            'test2',
        ];

        $object->setLabelAttributes($labelAttributes);

        $getLabelAttributes = $object->getLabelAttributes();

        $this->assertEquals($labelAttributes, $getLabelAttributes);
    }

    public function testSetEmptyLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [];

        $object->setLabelOptions($labelOptions);

        $this->assertEquals($labelOptions, []);
    }

    public function testSetLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [
            'foo'  => 'bar',
            'foo2' => 'bar2',
        ];

        $object->setLabelOptions($labelOptions);

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($retrievedLabelOptions, $labelOptions);
    }

    public function testSetLabelOptionsTraversable(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [
            'foo'  => 'bar',
            'foo2' => 'bar2',
        ];

        $object->setLabelOptions(new CustomTraversable($labelOptions));

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($retrievedLabelOptions, $labelOptions);
    }

    public function testGetEmptyLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = $object->getLabelOptions();

        $this->assertEquals($labelOptions, []);
    }

    public function testGetLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [
            'foo'  => 'bar',
            'foo2' => 'bar',
        ];

        $object->setLabelOptions($labelOptions);

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($labelOptions, $retrievedLabelOptions);
    }

    public function testClearLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [
            'foo'  => 'bar',
            'foo2' => 'bar',
        ];

        $object->setLabelOptions($labelOptions);

        $object->clearLabelOptions();

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals([], $retrievedLabelOptions);
    }

    public function testRemoveLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [
            'foo'  => 'bar',
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

    public function testSetLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $object->setLabelOption('foo', 'bar');

        $expectedLabelOptions = [
            'foo' => 'bar',
        ];

        $retrievedLabelOptions = $object->getLabelOptions();

        $this->assertEquals($expectedLabelOptions, $retrievedLabelOptions);
    }

    public function testGetInvalidLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $invalidOption = 'foo';

        $retrievedOption = $object->getLabelOption($invalidOption);

        $this->assertEquals(null, $retrievedOption);
    }

    public function testGetLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $option = 'foo';
        $value  = 'bar';

        $object->setLabelOption($option, $value);

        $retrievedValue = $object->getLabelOption($option);

        $this->assertEquals($retrievedValue, $value);
    }

    public function testRemoveLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $option = 'foo';
        $value  = 'bar';

        $object->setLabelOption($option, $value);

        $object->removeLabelOption($option);

        $retrievedValue = $object->getLabelOption($option);

        $this->assertEquals(null, $retrievedValue);
    }

    public function testHasValidLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $option = 'foo';
        $value  = 'bar';

        $object->setLabelOption($option, $value);

        $hasLabelOptionResult = $object->hasLabelOption($option);
        $this->assertTrue($hasLabelOptionResult);
    }

    public function testHasInvalidLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $option = 'foo';

        $hasLabelOptionResult = $object->hasLabelOption($option);
        $this->assertFalse($hasLabelOptionResult);
    }
}
