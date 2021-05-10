<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;

class FileInputFilterProviderForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => __NAMESPACE__ . '\FileInputFilterProviderFieldset',
            'name' => 'file_fieldset',
        ]);
    }
}
