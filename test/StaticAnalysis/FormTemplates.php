<?php

declare(strict_types=1);

namespace LaminasTest\Form\StaticAnalysis;

use Laminas\Form\FormInterface;
use LaminasTest\Form\StaticAnalysis\Asset\ExampleForm;

use function assert;
use function is_array;

final class FormTemplates
{
    /** @return non-empty-string */
    public function getStringTypeFromValidPayload(): string
    {
        $form = new ExampleForm();

        $validatedPayload = $form->getValidatedPayload();

        return $validatedPayload['string'];
    }

    public function getIntTypeFromValidPayload(): int
    {
        $form = new ExampleForm();

        return $form->getValidatedPayload()['number'];
    }

    /** @return non-empty-string */
    public function getSingleValueFromComposedInputFilter(): string
    {
        $form = new ExampleForm();

        return $form->getInputFilter()->getValues()['string'];
    }

    /** @return non-empty-string */
    public function getDataWithExplicitArray(): string
    {
        $form = new ExampleForm();
        $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

        return $data['string'];
    }

    /** @return non-empty-string */
    public function getDataWithValuesNormalized(): string
    {
        $form = new ExampleForm();
        $data = $form->getData(FormInterface::VALUES_NORMALIZED);
        assert(is_array($data));

        return $data['string'];
    }

    /** @return non-empty-string */
    public function testThatFluidReturnTypesPreserveTemplatesForSetPreferFormInputFilter(): string
    {
        $form = new ExampleForm();
        return $form->setPreferFormInputFilter(true)
            ->getData(FormInterface::VALUES_AS_ARRAY)['string'];
    }

    /** @return non-empty-string */
    public function testThatFluidReturnTypesPreserveTemplatesForSetWrapElements(): string
    {
        $form = new ExampleForm();
        return $form->setWrapElements(true)
            ->getData(FormInterface::VALUES_AS_ARRAY)['string'];
    }
}
