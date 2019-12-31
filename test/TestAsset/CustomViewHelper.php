<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\View\Helper\FormElement;

class CustomViewHelper extends AbstractHelper
{
    /**
     * @var FormElement
     */
    protected $elementHelper;

    public function __invoke(ElementInterface $element)
    {
        $elementHelper = $this->getElementHelper();

        $name = preg_replace('/[^a-z0-9_-]+/', '', $element->getName());

        $result = '<div id="custom' . $name . '">' . $elementHelper($element) . '</div>';

        return $result;
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
