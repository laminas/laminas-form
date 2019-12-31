<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Form\TestAsset;

use Laminas\Form\FieldsetInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\View\Helper\FormCollection as FormCollectionHelper;

class CustomFieldsetHelper extends AbstractHelper
{
    /**
     * @var FormCollection
     */
    protected $fieldsetHelper;

    public function __invoke(FieldsetInterface $fieldset)
    {
        $fieldsetHelper = $this->getFieldsetHelper();

        $name = preg_replace('/[^a-z0-9_-]+/', '', $fieldset->getName());
        $result = '<div id="customFieldset' . $name . '">' . $fieldsetHelper($fieldset) . '</div>';

        return $result;
    }

    /**
     * Retrieve the FormCollection helper
     *
     * @return FormCollection
     */
    protected function getFieldsetHelper()
    {
        if ($this->fieldsetHelper) {
            return $this->fieldsetHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->fieldsetHelper = $this->view->plugin('form_collection');
        }

        if (!$this->fieldsetHelper instanceof FormCollectionHelper) {
            $this->fieldsetHelper = new FormCollectionHelper();
        }

        return $this->fieldsetHelper;
    }
}
