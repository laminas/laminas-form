<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Email as EmailElement;
use PHPUnit_Framework_TestCase as TestCase;

class EmailTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesDefaultValidators()
    {
        $element = new EmailElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedValidators = array(
            'Laminas\Validator\Regex'
        );
        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
        }
    }

    public function emailAttributesDataProvider()
    {
        return array(
                  // attributes               // expectedValidators
            array(array('multiple' => true),  array('Laminas\Validator\Explode')),
            array(array('multiple' => false), array('Laminas\Validator\Regex')),
        );
    }

    /**
     * @dataProvider emailAttributesDataProvider
     */
    public function testProvidesInputSpecificationBasedOnAttributes($attributes, $expectedValidators)
    {
        $element = new EmailElement();
        $element->setAttributes($attributes);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
            switch ($class) {
                case 'Laminas\Validator\Explode':
                    $this->assertInstanceOf('Laminas\Validator\Regex', $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }
}
