<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\Annotation;

use Laminas\Filter\Boolean as BooleanFilter;

/**
 * AllowEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Laminas\InputFilter\Input should enable the allow_empty flag.
 *
 * @Annotation
 * @package    Laminas_Form
 * @subpackage Annotation
 */
class AllowEmpty
{
    /**
     * @var bool
     */
    protected $allow_empty = true;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value'])) {
            $data['value'] = false;
        }

        $allow_empty = $data['value'];

        if (!is_bool($allow_empty)) {
            $filter   = new BooleanFilter();
            $allow_empty = $filter->filter($allow_empty);
        }

        $this->allow_empty = $allow_empty;
    }

    /**
     * Get value of required flag
     *
     * @return bool
     */
    public function getAllowEmpty()
    {
        return $this->allow_empty;
    }
}
