<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\Validator\DateStep as DateStepValidator;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage Element
 */
class DateTimeLocal extends DateTime
{
    const DATETIME_LOCAL_FORMAT = 'Y-m-d\TH:i';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'datetime-local',
    );

    /**
     *
     * Opera and mobile browsers support datetime input, and display a datepicker control
     * But the submitted value does not include seconds.
     *
     * @var string
     */
    protected $format = self::DATETIME_LOCAL_FORMAT;

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     *
     * @return \Laminas\Validator\ValidatorInterface
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 1; // Minutes

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '1970-01-01T00:00';

        return new DateStepValidator(array(
            'format'    => $this->format,
            'baseValue' => $baseValue,
            'step'      => new \DateInterval("PT{$stepValue}M"),
        ));
    }
}
