<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\GreaterThan as GreaterThanValidator;
use Laminas\Validator\LessThan as LessThanValidator;
use Laminas\Validator\Regex as RegexValidator;
use Laminas\Validator\Step as StepValidator;
use Laminas\Validator\ValidatorInterface;

class Number extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'number',
    ];

    /** @var array */
    protected $validators = [];

    /**
     * Get validator
     *
     * @return ValidatorInterface[]
     */
    protected function getValidators(): array
    {
        if ($this->validators) {
            return $this->validators;
        }

        $validators = [];
        // HTML5 always transmits values in the format "1000.01", without a
        // thousand separator. The prior use of the i18n Float validator
        // allowed the thousand separator, which resulted in wrong numbers
        // when casting to float.
        $validators[] = new RegexValidator('(^-?\d*(\.\d+)?$)');

        $inclusive = true;
        if (isset($this->attributes['inclusive'])) {
            $inclusive = $this->attributes['inclusive'];
        }

        if (isset($this->attributes['min'])) {
            $validators[] = new GreaterThanValidator([
                'min'       => $this->attributes['min'],
                'inclusive' => $inclusive,
            ]);
        }
        if (isset($this->attributes['max'])) {
            $validators[] = new LessThanValidator([
                'max'       => $this->attributes['max'],
                'inclusive' => $inclusive,
            ]);
        }

        if (
            ! isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $validators[] = new StepValidator([
                'baseValue' => $this->attributes['min'] ?? 0,
                'step'      => $this->attributes['step'] ?? 1,
            ]);
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
    public function getInputSpecification(): array
    {
        return [
            'name'       => $this->getName(),
            'required'   => true,
            'filters'    => [
                ['name' => StringTrim::class],
            ],
            'validators' => $this->getValidators(),
        ];
    }
}
