<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Email as EmailElement;
use PHPUnit\Framework\TestCase;

use function get_class;

class EmailTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesDefaultValidators()
    {
        $element = new EmailElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertIsArray($inputSpec['validators']);

        $expectedValidators = [
            'Laminas\Validator\Regex',
        ];
        foreach ($inputSpec['validators'] as $i => $validator) {
            $class = get_class($validator);
            $this->assertEquals($expectedValidators[$i], $class);
        }
    }

    public function emailAttributesDataProvider()
    {
        return [
                  // attributes               // expectedValidators
            [['multiple' => true],  ['Laminas\Validator\Explode']],
            [['multiple' => false], ['Laminas\Validator\Regex']],
        ];
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
        $this->assertIsArray($inputSpec['validators']);

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
