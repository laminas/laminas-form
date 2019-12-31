<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

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
