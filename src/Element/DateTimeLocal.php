<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateInterval;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\ValidatorInterface;

class DateTimeLocal extends AbstractDateTime
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'datetime-local',
    ];

    /**
     * {@inheritDoc}
     */
    protected $format = 'Y-m-d\TH:i';

    /**
     * Retrieves a DateStepValidator configured for a Date Input type
     */
    protected function getStepValidator(): ValidatorInterface
    {
        $stepValue = $this->attributes['step'] ?? 1; // Minutes

        $baseValue = $this->attributes['min'] ?? '1970-01-01T00:00';

        return new DateStepValidator([
            'format'    => $this->format,
            'baseValue' => $baseValue,
            'step'      => new DateInterval("PT{$stepValue}M"),
        ]);
    }
}
