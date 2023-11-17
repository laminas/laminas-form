<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Validator\InArray as InArrayValidator;
use Laminas\Validator\ValidatorInterface;

class Radio extends MultiCheckbox
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'radio',
    ];

    /**
     * Get validator
     */
    protected function getValidator(): ?ValidatorInterface
    {
        if (null === $this->validator && ! $this->disableInArrayValidator()) {
            $this->validator = new InArrayValidator([
                'haystack' => $this->getValueOptionsValues(),
                'strict'   => false,
            ]);
        }
        return $this->validator;
    }
}
