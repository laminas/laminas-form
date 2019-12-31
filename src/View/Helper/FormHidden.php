<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;

/**
 * @category   Laminas
 * @package    Laminas_Form
 * @subpackage View
 */
class FormHidden extends FormInput
{
    /**
     * Attributes valid for the input tag type="hidden"
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'disabled'       => true,
        'form'           => true,
        'type'           => true,
        'value'          => true,
    );

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'hidden';
    }
}
