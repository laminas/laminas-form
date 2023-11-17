<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Explode as ExplodeValidator;
use Laminas\Validator\Regex as RegexValidator;
use Laminas\Validator\ValidatorInterface;

class Email extends Element implements InputProviderInterface
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'email',
    ];

    /** @var null|ValidatorInterface */
    protected $validator;

    /** @var null|ValidatorInterface */
    protected $emailValidator;

    /**
     * Get primary validator
     */
    public function getValidator(): ValidatorInterface
    {
        if (null === $this->validator) {
            $emailValidator = $this->getEmailValidator();

            $multiple = $this->attributes['multiple'] ?? null;

            if (true === $multiple || 'multiple' === $multiple) {
                $this->validator = new ExplodeValidator([
                    'validator' => $emailValidator,
                ]);
            } else {
                $this->validator = $emailValidator;
            }
        }

        return $this->validator;
    }

    /**
     * Sets the primary validator to use for this element
     *
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Get the email validator to use for multiple or single
     * email addresses.
     *
     * Note from the HTML5 Specs regarding the regex:
     *
     * "This requirement is a *willful* violation of RFC 5322, which
     * defines a syntax for e-mail addresses that is simultaneously
     * too strict (before the "@" character), too vague
     * (after the "@" character), and too lax (allowing comments,
     * whitespace characters, and quoted strings in manners
     * unfamiliar to most users) to be of practical use here."
     *
     * The default Regex validator is in use to match that of the
     * browser validation, but you are free to set a different
     * (more strict) email validator such as Laminas\Validator\Email
     * if you wish.
     */
    public function getEmailValidator(): ValidatorInterface
    {
        if (null === $this->emailValidator) {
            $this->emailValidator = new RegexValidator(
                '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/'
            );
        }
        return $this->emailValidator;
    }

    /**
     * Sets the email validator to use for multiple or single
     * email addresses.
     *
     * @return $this
     */
    public function setEmailValidator(ValidatorInterface $validator)
    {
        $this->emailValidator = $validator;
        return $this;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches an email validator.
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
            'validators' => [
                $this->getValidator(),
            ],
        ];

        $name = $this->getName();
        if ($name !== null) {
            $spec['name'] = $name;
        }

        return $spec;
    }
}
