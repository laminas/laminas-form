<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form;
use Zend\InputFilter\ArrayInput;
use Zend\InputFilter\InputFilterProviderInterface;

class ArrayInputCollectionForm extends Form\Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('arrayInputCollectionForm');

        $this->add([
            'type' => Form\Element\Collection::class,
            'name' => 'foo',
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'template_placeholder' => '__index__',
                'target_element' => [
                    'type' => Form\Element\Text::class,
                ],
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'foo' => [
                'type' => ArrayInput::class,
                'required' => true,
            ],
        ];
    }
}
