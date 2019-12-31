<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Form\Element\Number as NumberElement;
use Laminas\I18n\Validator\Float as NumberValidator;
use Laminas\Validator\GreaterThan as GreaterThanValidator;
use Laminas\Validator\LessThan as LessThanValidator;
use Laminas\Validator\Step as StepValidator;

class Range extends NumberElement
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'range',
    );

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
        $validators[] = new NumberValidator();

        $inclusive = true;
        if (!empty($this->attributes['inclusive'])) {
            $inclusive = $this->attributes['inclusive'];
        }

        $validators[] = new GreaterThanValidator(array(
            'min'       => (isset($this->attributes['min'])) ? $this->attributes['min'] : 0,
            'inclusive' => $inclusive
        ));

        $validators[] = new LessThanValidator(array(
            'max'       => (isset($this->attributes['max'])) ? $this->attributes['max'] : 100,
            'inclusive' => $inclusive
        ));

        if (!isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $validators[] = new StepValidator(array(
                'baseValue' => (isset($this->attributes['min'])) ? $this->attributes['min'] : 0,
                'step'      => (isset($this->attributes['step'])) ? $this->attributes['step'] : 1,
            ));
        }

        $this->validators = $validators;
        return $this->validators;
    }
}
