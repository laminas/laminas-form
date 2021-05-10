<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class FileInputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => 'file',
            'name' => 'file_field',
            'options' => [
                'label' => 'File Label',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'file_field' => [
                'type'     => 'Laminas\InputFilter\FileInput',
                'filters'  => [
                    [
                        'name' => 'filerenameupload',
                        'options' => [
                            'target'    => __FILE__,
                            'randomize' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
