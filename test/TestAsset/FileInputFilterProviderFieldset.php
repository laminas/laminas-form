<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilterProviderInterface;

class FileInputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @inheritDoc
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type'    => 'file',
            'name'    => 'file_field',
            'options' => [
                'label' => 'File Label',
            ],
        ]);
    }

    /**
     * @return array[]
     */
    public function getInputFilterSpecification()
    {
        return [
            'file_field' => [
                'type'    => FileInput::class,
                'filters' => [
                    [
                        'name'    => 'filerenameupload',
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
