<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\InArray as InArrayValidator;
use Laminas\Validator\ValidatorInterface;

class Checkbox extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'checkbox',
    ];

    /** @var null|ValidatorInterface */
    protected $validator;

    /** @var bool */
    protected $useHiddenElement = true;

    /** @var string */
    protected $uncheckedValue = '0';

    /** @var string */
    protected $checkedValue = '1';

    /**
     * Accepted options for MultiCheckbox:
     * - use_hidden_element: do we render hidden element?
     * - unchecked_value: value for checkbox when unchecked
     * - checked_value: value for checkbox when checked
     *
     * @return $this
     */
    public function setOptions(iterable $options)
    {
        parent::setOptions($options);

        if (isset($this->options['use_hidden_element'])) {
            $this->setUseHiddenElement($this->options['use_hidden_element']);
        }

        if (isset($this->options['unchecked_value'])) {
            $this->setUncheckedValue($this->options['unchecked_value']);
        }

        if (isset($this->options['checked_value'])) {
            $this->setCheckedValue($this->options['checked_value']);
        }

        return $this;
    }

    /**
     * Do we render hidden element?
     *
     * @return $this
     */
    public function setUseHiddenElement(bool $useHiddenElement)
    {
        $this->useHiddenElement = $useHiddenElement;
        return $this;
    }

    /**
     * Do we render hidden element?
     */
    public function useHiddenElement(): bool
    {
        return $this->useHiddenElement;
    }

    /**
     * Set the value to use when checkbox is unchecked
     *
     * @return $this
     */
    public function setUncheckedValue(string $uncheckedValue)
    {
        $this->uncheckedValue = $uncheckedValue;
        return $this;
    }

    /**
     * Get the value to use when checkbox is unchecked
     */
    public function getUncheckedValue(): string
    {
        return $this->uncheckedValue;
    }

    /**
     * Set the value to use when checkbox is checked
     *
     * @return $this
     */
    public function setCheckedValue(string $checkedValue)
    {
        $this->checkedValue = $checkedValue;
        return $this;
    }

    /**
     * Get the value to use when checkbox is checked
     */
    public function getCheckedValue(): string
    {
        return $this->checkedValue;
    }

    /**
     * Get validator
     */
    protected function getValidator(): ?ValidatorInterface
    {
        if (null === $this->validator) {
            $this->validator = new InArrayValidator([
                'haystack' => [$this->checkedValue, $this->uncheckedValue],
                'strict'   => false,
            ]);
        }
        return $this->validator;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches the captcha as a validator.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $spec = [
            'name'     => $this->getName(),
            'required' => true,
        ];

        if ($validator = $this->getValidator()) {
            $spec['validators'] = [
                $validator,
            ];
        }

        return $spec;
    }

    /**
     * Checks if this checkbox is checked.
     */
    public function isChecked(): bool
    {
        return $this->value === $this->getCheckedValue();
    }

    /**
     * Checks or unchecks the checkbox.
     *
     * @param bool $value The flag to set.
     * @return $this
     */
    public function setChecked(bool $value)
    {
        $this->value = $value ? $this->getCheckedValue() : $this->getUncheckedValue();
        return $this;
    }

    /**
     * Checks or unchecks the checkbox.
     *
     * @param  mixed $value A boolean flag or string that is checked against the "checked value".
     * @return $this
     */
    public function setValue($value)
    {
        // Cast to strings because POST data comes in string form
        $checked     = (string) $value === $this->getCheckedValue();
        $this->value = $checked ? $this->getCheckedValue() : $this->getUncheckedValue();
        return $this;
    }
}
