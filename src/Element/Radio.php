<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Element;

use Laminas\Validator\InArray as InArrayValidator;
use Laminas\Validator\ValidatorInterface;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage Element
 */
class Radio extends MultiCheckbox
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'radio'
    );

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new InArrayValidator(array(
                'haystack'  => $this->getValueOptionsValues(),
                'strict'    => false,
            ));
        }
        return $this->validator;
    }
}
