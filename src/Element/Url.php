<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Uri as UriValidator;

class Url extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'url',
    );

    /**
     * @var \Laminas\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * Get validator
     *
     * @return \Laminas\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new UriValidator(array(
                'allowAbsolute' => true,
                'allowRelative' => false,
            ));
        }
        return $this->validator;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches an email validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Laminas\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }
}
