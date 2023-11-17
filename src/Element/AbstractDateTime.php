<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use DateInterval;
use DateTime as PhpDateTime;
use DateTimeInterface;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Date as DateValidator;
use Laminas\Validator\DateStep as DateStepValidator;
use Laminas\Validator\GreaterThan as GreaterThanValidator;
use Laminas\Validator\LessThan as LessThanValidator;
use Laminas\Validator\ValidatorInterface;

use function date;
use function sprintf;

abstract class AbstractDateTime extends Element implements InputProviderInterface
{
    /**
     * A valid format string accepted by date()
     *
     * @var string
     */
    protected $format = 'Y-m-d\TH:iP';

    /** @var array<ValidatorInterface> */
    protected $validators = [];

    /**
     * Accepted options for DateTime:
     * - format: A \DateTime compatible string
     *
     * @return $this
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['format'])) {
            $this->setFormat($this->options['format']);
        }

        return $this;
    }

    /**
     * Retrieve the element value
     *
     * If the value is instance of DateTimeInterface, and $returnFormattedValue
     * is true (the default), we return the string representation using the
     * currently registered format.
     *
     * If $returnFormattedValue is false, the original value will be
     * returned, regardless of type.
     *
     * @return mixed
     */
    public function getValue(bool $returnFormattedValue = true)
    {
        $value = parent::getValue();
        if (! $value instanceof DateTimeInterface || ! $returnFormattedValue) {
            return $value;
        }
        $format = $this->getFormat();
        return $value->format($format);
    }

    /**
     * Set value for format
     *
     * @return $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Retrieve the DateTime format to use for the value
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get validators
     *
     * @return array<ValidatorInterface>
     */
    protected function getValidators(): array
    {
        if ($this->validators) {
            return $this->validators;
        }

        $validators   = [];
        $validators[] = $this->getDateValidator();

        if (
            isset($this->attributes['min'])
            && $this->valueIsValidDateTimeFormat((string) $this->attributes['min'])
        ) {
            $validators[] = new GreaterThanValidator([
                'min'       => $this->attributes['min'],
                'inclusive' => true,
            ]);
        } elseif (
            isset($this->attributes['min'])
            && ! $this->valueIsValidDateTimeFormat((string) $this->attributes['min'])
        ) {
            throw new InvalidArgumentException(sprintf(
                '%1$s expects "min" to conform to %2$s; received "%3$s"',
                __METHOD__,
                $this->format,
                (string) $this->attributes['min']
            ));
        }

        if (
            isset($this->attributes['max'])
            && $this->valueIsValidDateTimeFormat((string) $this->attributes['max'])
        ) {
            $validators[] = new LessThanValidator([
                'max'       => $this->attributes['max'],
                'inclusive' => true,
            ]);
        } elseif (
            isset($this->attributes['max'])
            && ! $this->valueIsValidDateTimeFormat((string) $this->attributes['max'])
        ) {
            throw new InvalidArgumentException(sprintf(
                '%1$s expects "max" to conform to %2$s; received "%3$s"',
                __METHOD__,
                $this->format,
                (string) $this->attributes['max']
            ));
        }
        if (
            ! isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $validators[] = $this->getStepValidator();
        }

        $this->validators = $validators;
        return $this->validators;
    }

    /**
     * Retrieves a Date Validator configured for a DateTime Input type
     */
    protected function getDateValidator(): ValidatorInterface
    {
        return new DateValidator(['format' => $this->format]);
    }

    /**
     * Retrieves a DateStep Validator configured for a DateTime Input type
     */
    protected function getStepValidator(): ValidatorInterface
    {
        $format    = $this->getFormat();
        $stepValue = $this->attributes['step'] ?? 1; // Minutes

        $baseValue = $this->attributes['min'] ?? date($format, 0);

        return new DateStepValidator([
            'format'    => $format,
            'baseValue' => $baseValue,
            'step'      => new DateInterval("PT{$stepValue}M"),
        ]);
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches default validators for the datetime input.
     *
     * @inheritDoc
     */
    public function getInputSpecification(): array
    {
        $spec = [
            'required'   => true,
            'filters'    => [
                ['name' => StringTrim::class],
            ],
            'validators' => $this->getValidators(),
        ];

        $name = $this->getName();
        if ($name !== null) {
            $spec['name'] = $name;
        }

        return $spec;
    }

    /**
     * Indicate whether or not a value represents a valid DateTime format.
     */
    private function valueIsValidDateTimeFormat(string $value): bool
    {
        return PhpDateTime::createFromFormat(
            $this->format,
            $value
        ) instanceof DateTimeInterface;
    }
}
