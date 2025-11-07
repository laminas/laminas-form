<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateInterval;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\ValidatorInterface;

use function date;

class Time extends AbstractDateTime
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'time',
    ];

    /**
     * Default date format
     *
     * @var string
     */
    protected $format = 'H:i:s';

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     */
    protected function getStepValidator(): ValidatorInterface
    {
        $format    = $this->getFormat();
        $stepValue = $this->attributes['step'] ?? 60; // Seconds

        $baseValue = $this->attributes['min'] ?? date($format, 0);

        return new DateStepValidator([
            'format'    => $format,
            'baseValue' => $baseValue,
            'step'      => new DateInterval("PT{$stepValue}S"),
        ]);
    }
}
