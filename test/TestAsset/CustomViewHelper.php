<?php

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\View\Helper\FormElement;

use function method_exists;
use function preg_replace;

class CustomViewHelper extends AbstractHelper
{
    /** @var FormElement */
    protected $elementHelper;

    public function __invoke(ElementInterface $element)
    {
        $elementHelper = $this->getElementHelper();

        $name = preg_replace('/[^a-z0-9_-]+/', '', $element->getName());

        return '<div id="custom' . $name . '">' . $elementHelper($element) . '</div>';
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (! $this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }
}
