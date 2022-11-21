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
final class LabelAwareTraitTest extends TestCase
{
    public function testSetLabelAttributes(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        self::assertEmpty($object->getLabelAttributes());

        $labelAttributes = [
            'test',
            'test2',
        ];

        $object->setLabelAttributes($labelAttributes);

        self::assertSame($labelAttributes, $object->getLabelAttributes());
    }

    public function testGetEmptyLabelAttributes(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        self::assertEmpty($object->getLabelAttributes());
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

        self::assertEquals($labelAttributes, $getLabelAttributes);
    }

    public function testSetEmptyLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = [];

        $object->setLabelOptions($labelOptions);

        self::assertEquals($labelOptions, []);
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

        self::assertEquals($retrievedLabelOptions, $labelOptions);
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

        self::assertEquals($retrievedLabelOptions, $labelOptions);
    }

    public function testGetEmptyLabelOptions(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $labelOptions = $object->getLabelOptions();

        self::assertEquals($labelOptions, []);
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

        self::assertEquals($labelOptions, $retrievedLabelOptions);
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

        self::assertEquals([], $retrievedLabelOptions);
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

        self::assertEquals($expectedLabelOptions, $retrievedLabelOptions);
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

        self::assertEquals($expectedLabelOptions, $retrievedLabelOptions);
    }

    public function testGetInvalidLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $invalidOption = 'foo';

        $retrievedOption = $object->getLabelOption($invalidOption);

        self::assertEquals(null, $retrievedOption);
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

        self::assertEquals($retrievedValue, $value);
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

        self::assertEquals(null, $retrievedValue);
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
        self::assertTrue($hasLabelOptionResult);
    }

    public function testHasInvalidLabelOption(): void
    {
        $object = new class {
            use LabelAwareTrait;
        };

        $option = 'foo';

        $hasLabelOptionResult = $object->hasLabelOption($option);
        self::assertFalse($hasLabelOptionResult);
    }
}
