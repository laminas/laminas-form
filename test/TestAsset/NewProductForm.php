<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;

/** @extends Form<array<string, mixed>> */
class NewProductForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_product');

        $this
            ->setAttribute('method', 'post')
            ->setHydrator(new ClassMethodsHydrator());

        $fieldset = new ProductFieldset();
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type' => 'submit',
            ],
        ]);
    }
}
