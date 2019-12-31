<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\Factory;
use PHPUnit_Framework_TestCase as TestCase;

class SelectTest extends TestCase
{
    public function testProvidesInputSpecificationForSingleSelect()
    {
        $element = new SelectElement();
        $element->setValueOptions(array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Laminas\Validator\InArray'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
        }
    }

    public function testProvidesInputSpecificationForMultipleSelect()
    {
        $element = new SelectElement();
        $element->setAttributes(array(
            'multiple' => true,
        ));
        $element->setValueOptions(array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ));

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Laminas\Validator\Explode'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Laminas\Validator\Explode':
                    $this->assertInstanceOf('Laminas\Validator\InArray', $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }

    public function selectOptionsDataProvider()
    {
        return array(
            array(
                array('foo', 'bar'),
                array(
                    'foo' => 'My Foo Label',
                    'bar' => 'My Bar Label',
                )
            ),
            array(
                array('foo', 'bar'),
                array(
                    0 => array('label' => 'My Foo Label', 'value' => 'foo'),
                    1 => array('label' => 'My Bar Label', 'value' => 'bar'),
                )
            ),
        );
    }

    /**
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new SelectElement('my-select');
        $element->setValueOptions($options);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Laminas\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    public function testOptionsHasArrayOnConstruct()
    {
        $element = new SelectElement();
        $this->assertTrue(is_array($element->getValueOptions()));
    }

    public function testDeprecateOptionsInAttributes()
    {
        $element = new SelectElement();
        $valueOptions = array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        );
        $element->setAttributes(array(
            'multiple' => true,
            'options'  => $valueOptions,
        ));
        $this->assertEquals($valueOptions, $element->getValueOptions());
    }
}