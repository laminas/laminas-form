<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis;

use Exception;
use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\Form;

use function array_keys;
use function array_values;
use function iterator_to_array;

/** @psalm-suppress UnusedClass, UnusedMethod */
final class FormIterableTypes
{
    public function firstElement(Form $form): ElementInterface
    {
        foreach ($form as $element) {
            return $element;
        }

        throw new Exception('Form is empty');
    }

    /** @return list<ElementInterface> */
    public function elementList(FieldsetInterface $fieldset): array
    {
        return array_values(iterator_to_array($fieldset));
    }

    /** @return list<string> */
    public function elementNames(FieldsetInterface $fieldset): array
    {
        return array_keys(iterator_to_array($fieldset));
    }
}
