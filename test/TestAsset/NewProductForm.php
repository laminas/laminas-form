<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;

class NewProductForm extends Form
{
    public function __construct()
    {
        parent::__construct('create_product');

        $this
            ->setAttribute('method', 'post')
            ->setHydrator(new ClassMethodsHydrator())
            ->setInputFilter(new InputFilter());

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
