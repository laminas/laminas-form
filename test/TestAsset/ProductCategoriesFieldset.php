<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

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
