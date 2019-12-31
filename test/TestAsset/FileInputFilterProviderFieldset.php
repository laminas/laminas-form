<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class FileInputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'file',
            'name' => 'file_field',
            'options' => array(
                'label' => 'File Label',
            ),
        ));

    }

    public function getInputFilterSpecification()
    {
        return array(
            'file_field' => array(
                'type'     => 'Laminas\InputFilter\FileInput',
                'filters'  => array(
                    array(
                        'name' => 'filerenameupload',
                        'options' => array(
                            'target'    => __FILE__,
                            'randomize' => true,
                        ),
                    ),
                ),
            ),
        );
    }
}
