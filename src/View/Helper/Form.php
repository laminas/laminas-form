<?php

declare(strict_types=1);

namespace Laminas\Form\View\Helper;

use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\View\Helper\Doctype;
use Laminas\View\Renderer\PhpRenderer;

use function array_key_exists;
use function array_merge;
use function assert;
use function method_exists;
use function sprintf;

/**
 * View helper for rendering Form objects
 */
class Form extends AbstractHelper
{
    /**
     * Attributes valid for this tag (form)
     *
     * @var array
     */
    protected $validTagAttributes = [
        'accept-charset' => true,
        'action'         => true,
        'autocomplete'   => true,
        'enctype'        => true,
        'method'         => true,
        'name'           => true,
        'novalidate'     => true,
        'target'         => true,
    ];

    /**
     * Invoke as function
     *
     * @template T as null|FormInterface
     * @psalm-param T $form
     * @psalm-return (T is null ? self : string)
     * @return Form|string
     */
    public function __invoke(?FormInterface $form = null)
    {
        if (! $form) {
            return $this;
        }

        return $this->render($form);
    }

    /**
     * Render a form from the provided $form,
     */
    public function render(FormInterface $form): string
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        $renderer = $this->getView();
        assert($renderer instanceof PhpRenderer);
        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent .= $renderer->formCollection($element);
            } else {
                $formContent .= $renderer->formRow($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }

    /**
     * Generate an opening form tag
     */
    public function openTag(?FormInterface $form = null): string
    {
        $doctype    = $this->getDoctype();
        $attributes = [];

        if (! (Doctype::HTML5 === $doctype || Doctype::XHTML5 === $doctype)) {
            $attributes = [
                'action' => '',
                'method' => 'get',
            ];
        }

        if ($form instanceof FormInterface) {
            $formAttributes = $form->getAttributes();
            if (! array_key_exists('id', $formAttributes) && array_key_exists('name', $formAttributes)) {
                $formAttributes['id'] = $formAttributes['name'];
            }
            $attributes = array_merge($attributes, $formAttributes);
        }

        if ($attributes) {
            return sprintf('<form %s>', $this->createAttributesString($attributes));
        }

        return '<form>';
    }

    /**
     * Generate a closing form tag
     */
    public function closeTag(): string
    {
        return '</form>';
    }
}
