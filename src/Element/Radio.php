<?php

namespace Laminas\Form\Element;

use Laminas\Validator\InArray as InArrayValidator;
use Laminas\Validator\ValidatorInterface;

class Radio extends MultiCheckbox
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'radio',
    ];

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator && ! $this->disableInArrayValidator()) {
            $this->validator = new InArrayValidator([
                'haystack'  => $this->getValueOptionsValues(),
                'strict'    => false,
            ]);
        }
        return $this->validator;
    }
}
