<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function sprintf;

class FormImage extends FormInput
{
    /**
     * Attributes valid for the input tag type="image"
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'           => true,
        'alt'            => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'height'         => true,
        'src'            => true,
        'type'           => true,
        'width'          => true,
    ];

    /**
     * Render a form <input> element from the provided $element
     *
     * @throws Exception\DomainException
     */
    public function render(ElementInterface $element): string
    {
        $src = $element->getAttribute('src');
        if (empty($src)) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned src; none discovered',
                __METHOD__
            ));
        }

        return parent::render($element);
    }

    /**
     * Determine input type to use
     */
    protected function getType(ElementInterface $element): string
    {
        return 'image';
    }
}
