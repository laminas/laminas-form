<?php

declare(strict_types=1);

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Form;

/** @extends Form<array<string, mixed>> */
class FileInputFilterProviderForm extends Form
{
    /**
     * @inheritDoc
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => FileInputFilterProviderFieldset::class,
            'name' => 'file_fieldset',
        ]);
    }
}
