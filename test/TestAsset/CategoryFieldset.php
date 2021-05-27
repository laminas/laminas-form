<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
use LaminasTest\Form\TestAsset\Entity\Category;

class CategoryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('category');
        $this
            ->setHydrator(new ClassMethodsHydrator())
            ->setObject(new Category());

        $this->add([
            'name'       => 'name',
            'options'    => [
                'label' => 'Name of the category',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
            ],
        ];
    }
}
