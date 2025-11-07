<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateInterval;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\Regex as RegexValidator;
use Laminas\Validator\ValidatorInterface;

class Month extends AbstractDateTime
{
    /**
     * A valid format string accepted by date()
     *
     * @var string
     */
    protected $format = '!Y-m';

    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'month',
    ];

    /**
     * Retrieves a Date Validator configured for a Month Input type
     */
    protected function getDateValidator(): ValidatorInterface
    {
        return new RegexValidator('/^[0-9]{4}\-(0[1-9]|1[012])$/');
    }

    /**
     * Retrieves a DateStep Validator configured for a Month Input type
     */
    protected function getStepValidator(): ValidatorInterface
    {
        $stepValue = $this->attributes['step'] ?? 1; // Months

        $baseValue = $this->attributes['min'] ?? '1970-01';

        return new DateStepValidator([
            'format'    => 'Y-m',
            'baseValue' => $baseValue,
            'step'      => new DateInterval("P{$stepValue}M"),
        ]);
    }
}
