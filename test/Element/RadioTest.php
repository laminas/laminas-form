<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Radio as RadioElement;
use PHPUnit_Framework_TestCase as TestCase;

class RadioTest extends TestCase
{
    public function useHiddenAttributeDataProvider()
    {
        return array(array(true), array(false));
    }

    /**
     * @dataProvider useHiddenAttributeDataProvider
     */
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes($useHiddenElement)
    {
        $element = new RadioElement();
        $options = array(
            '1' => 'Option 1',
            '2' => 'Option 2',
            '3' => 'Option 3',
        );
        $element->setAttributes(array(
            'options' => $options,
        ));
        $element->setUseHiddenElement($useHiddenElement);

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

    public function radioOptionsDataProvider()
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
     * @dataProvider radioOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new RadioElement('my-radio');
        $element->setAttributes(array(
            'options' => $options,
        ));
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Laminas\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }
}
