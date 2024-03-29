<?php

declare(strict_types=1);

namespace Laminas\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Uri as UriValidator;
use Laminas\Validator\ValidatorInterface;

class Url extends Element implements InputProviderInterface
{
    /** @var array<string, scalar|null>  */
    protected $attributes = [
        'type' => 'url',
    ];

    /** @var null|ValidatorInterface */
    protected $validator;

    /**
     * Get validator
     */
    public function getValidator(): ValidatorInterface
    {
        if (null === $this->validator) {
            $this->validator = new UriValidator([
                'allowAbsolute' => true,
                'allowRelative' => false,
            ]);
        }
        return $this->validator;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches an uri validator.
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
