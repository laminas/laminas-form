<?php

namespace LaminasTest\Form\TestAsset;

class ProductCategoriesFieldset extends ProductFieldset
{
    public function __construct()
    {
        parent::__construct();

        $template = new CategoryFieldset();

        $this->add([
            'name' => 'categories',
            'type' => 'collection',
            'options' => [
                'label' => 'Categories',
                'should_create_template' => true,
                'allow_add' => true,
                'count' => 0,
                'target_element' => $template,
            ],
        ]);
    }
}
