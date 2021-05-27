<?php

declare(strict_types=1);

namespace LaminasTest\Form\Integration;

use Laminas\Form\Form;
use Laminas\Form\InputFilterProviderFieldset;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\Validator;
use PHPUnit\Framework\TestCase;

use function sprintf;

class FormCreatesCollectionInputFilterTest extends TestCase
{
    public static function assertValidatorFound(string $class, array $validators, ?string $message = null): void
    {
        $message = $message ?: sprintf('Failed to find validator of type %s in validator list', $class);
        foreach ($validators as $instance) {
            $validator = $instance['instance'];
            if ($validator instanceof $class) {
                return;
            }
        }
        self::fail($message);
    }

    /**
     * @see https://github.com/zendframework/zend-form/issues/78
     */
    public function testCollectionInputFilterContainsExpectedValidators(): Form
    {
        $form = new Form();
        $form->add([
            'name'    => 'collection',
            'type'    => 'collection',
            'options' => [
                'target_element' => [
                    'type'     => InputFilterProviderFieldset::class,
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'date',
                                'type' => 'date',
                            ],
                        ],
                    ],
                    'options'  => [
                        'input_filter_spec' => [
                            'date' => [
                                'required'   => false,
                                'validators' => [
                                    ['name' => 'StringLength'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $inputFilter           = $form->getInputFilter();
        $inputFilterCollection = $inputFilter->get('collection');
        $this->assertInstanceOf(CollectionInputFilter::class, $inputFilterCollection);
        $filter = $inputFilterCollection->getInputFilter()->get('date');

        $validators = $filter->getValidatorChain()->getValidators();
        $this->assertCount(3, $validators);
        $this->assertValidatorFound(Validator\StringLength::class, $validators);
        $this->assertValidatorFound(Validator\Date::class, $validators);
        $this->assertValidatorFound(Validator\DateStep::class, $validators);

        return $form;
    }

    /**
     * @depends testCollectionInputFilterContainsExpectedValidators
     */
    public function testCollectionElementDoesNotCreateDiscreteElementInInputFilter(Form $form): void
    {
        $inputFilter = $form->getInputFilter();
        $this->assertFalse($inputFilter->has('date'));
    }
}
