<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\I18n\Validator\Float as NumberValidator;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\GreaterThan as GreaterThanValidator;
use Laminas\Validator\LessThan as LessThanValidator;
use Laminas\Validator\Step as StepValidator;

class Number extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'number',
    );

    /**
     * @var array
     */
    protected $validators;

    /**
     * Get validator
     *
     * @return \Laminas\Validator\ValidatorInterface[]
     */
    protected function getValidators()
    {
        if ($this->validators) {
            return $this->validators;
        }

        $validators = array();
        $validators[] = new NumberValidator(array(
            'locale' => 'en_US', // HTML5 uses "100.01" format
        ));

        $inclusive = true;
        if (!empty($this->attributes['inclusive'])) {
            $inclusive = $this->attributes['inclusive'];
        }

        if (isset($this->attributes['min'])) {
            $validators[] = new GreaterThanValidator(array(
                'min' => $this->attributes['min'],
                'inclusive' => $inclusive
            ));
        }
        if (isset($this->attributes['max'])) {
            $validators[] = new LessThanValidator(array(
                'max' => $this->attributes['max'],
                'inclusive' => $inclusive
            ));
        }

        if (!isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $validators[] = new StepValidator(array(
                'baseValue' => (isset($this->attributes['min']))  ? $this->attributes['min'] : 0,
                'step'      => (isset($this->attributes['step'])) ? $this->attributes['step'] : 1,
            ));
        }

        $this->validators = $validators;
        return $this->validators;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches a number validator, as well as a greater than and less than validators
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Laminas\Filter\StringTrim')
            ),
            'validators' => $this->getValidators(),
        );
    }
}
