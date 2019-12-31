<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Date as DateValidator;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\ValidatorInterface;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage Element
 */
class Time extends DateTime
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'time',
    );

    /**
     * Retrieves a Date Validator configured for a DateTime Input type
     *
     * @return ValidatorInterface
     */
    protected function getDateValidator()
    {
        return new DateValidator(array('format' => 'H:i:s'));
    }

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     *
     * @return ValidatorInterface
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 60; // Seconds

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '00:00:00';

        return new DateStepValidator(array(
            'format'    => 'H:i:s',
            'baseValue' => $baseValue,
            'step'      => new \DateInterval("PT{$stepValue}S"),
        ));
    }
}
