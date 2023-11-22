<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis;

use Laminas\Form\Element\MultiCheckbox;

/** @psalm-suppress UnusedClass, UnusedMethod */
final class MultiCheckboxValueOptions
{
    public function checkValueOptionsVariations(): void
    {
        $element = new MultiCheckbox('foo');
        $element->setValueOptions([
            'a' => 'A',
            'b' => 'B',
        ]);

        $element = new MultiCheckbox('foo');
        $element->setValueOptions([
            [
                'label' => 'Foo',
                'value' => 'Bar',
            ],
            [
                'label'    => 'Foo',
                'value'    => 'Bar',
                'disabled' => false,
            ],
            [
                'label'    => 'Foo',
                'value'    => 'Bar',
                'disabled' => true,
                'selected' => true,
            ],
            [
                'label'      => 'Foo',
                'value'      => 'Bar',
                'attributes' => [
                    'readonly'  => true,
                    'something' => 'else',
                ],
            ],
            [
                'label'            => 'Foo',
                'value'            => 'Bar',
                'label_attributes' => [
                    'inert' => true,
                    'class' => 'baz',
                ],
            ],
        ]);

        $element = new MultiCheckbox('foo');
        $element->setValueOptions([
            'foo' => 'bar',
            [
                'label'            => 'Foo',
                'value'            => 'Bar',
                'disabled'         => true,
                'selected'         => true,
                'attributes'       => [
                    'readonly'  => true,
                    'something' => 'else',
                ],
                'label_attributes' => [
                    'inert' => true,
                    'class' => 'baz',
                ],
            ],
            'bing' => 'bong',
        ]);
    }
}
