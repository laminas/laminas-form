<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateInterval;
use DateTimeZone;
use Laminas\Form\Element\AbstractDateTime as DateTimeElement;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\ValidatorInterface;

use function date;

class Date extends DateTimeElement
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'date',
    ];

    /**
     * Date format to use for DateTime values. By default, this is RFC-3339,
     * full-date (Y-m-d), which is what HTML5 dictates.
     *
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * Retrieves a DateStep Validator configured for a Date Input type
     */
    protected function getStepValidator(): ValidatorInterface
    {
        $format    = $this->getFormat();
        $stepValue = $this->attributes['step'] ?? 1; // Days

        $baseValue = $this->attributes['min'] ?? date($format, 0);

        return new DateStepValidator([
            'format'    => $format,
            'baseValue' => $baseValue,
            'timezone'  => new DateTimeZone('UTC'),
            'step'      => new DateInterval("P{$stepValue}D"),
        ]);
    }
}
