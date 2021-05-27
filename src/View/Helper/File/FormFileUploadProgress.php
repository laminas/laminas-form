<?php

namespace Laminas\Form\View\Helper\File;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormInput;

use function sprintf;
use function uniqid;

/**
 * A view helper to render the hidden input with a UploadProgress id
 * for file uploads progress tracking.
 */
class FormFileUploadProgress extends FormInput
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @return string
     */
    public function __invoke(?ElementInterface $element = null): string
    {
        return $this->renderHiddenId();
    }

    /**
     * Render a hidden form <input> element with the progress id
     *
     * @return string
     */
    public function renderHiddenId(): string
    {
        $attributes = [
            'id'    => 'progress_key',
            'name'  => $this->getName(),
            'type'  => 'hidden',
            'value' => $this->getValue(),
        ];

        return sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'UPLOAD_IDENTIFIER';
    }

    /**
     * @return string
     */
    protected function getValue(): string
    {
        return uniqid();
    }
}
