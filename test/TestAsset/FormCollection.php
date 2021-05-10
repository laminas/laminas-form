<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Element\Color as ColorElement;
use Laminas\Form\Form;

class FormCollection extends Form
{
    public function __construct()
    {
        parent::__construct('collection');
        $this->setInputFilter(new InputFilter());

        $element = new ColorElement('color');
        $this->add([
            'type' => 'Laminas\Form\Element\Collection',
            'name' => 'colors',
            'options' => [
                'count' => 2,
                'target_element' => $element,
            ],
        ]);

        $fieldset = new BasicFieldset();
        $this->add([
            'type' => 'Laminas\Form\Element\Collection',
            'name' => 'fieldsets',
            'options' => [
                'count' => 2,
                'target_element' => $fieldset,
            ],
        ]);
    }
}
