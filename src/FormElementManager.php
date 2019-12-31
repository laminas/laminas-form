<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\InitializableInterface;

/**
 * Plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class FormElementManager extends AbstractPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'button'        => 'Laminas\Form\Element\Button',
        'captcha'       => 'Laminas\Form\Element\Captcha',
        'checkbox'      => 'Laminas\Form\Element\Checkbox',
        'collection'    => 'Laminas\Form\Element\Collection',
        'color'         => 'Laminas\Form\Element\Color',
        'csrf'          => 'Laminas\Form\Element\Csrf',
        'date'          => 'Laminas\Form\Element\Date',
        'dateselect'    => 'Laminas\Form\Element\DateSelect',
        'datetime'      => 'Laminas\Form\Element\DateTime',
        'datetimelocal' => 'Laminas\Form\Element\DateTimeLocal',
        'datetimeselect' => 'Laminas\Form\Element\DateTimeSelect',
        'element'       => 'Laminas\Form\Element',
        'email'         => 'Laminas\Form\Element\Email',
        'fieldset'      => 'Laminas\Form\Fieldset',
        'file'          => 'Laminas\Form\Element\File',
        'form'          => 'Laminas\Form\Form',
        'hidden'        => 'Laminas\Form\Element\Hidden',
        'image'         => 'Laminas\Form\Element\Image',
        'month'         => 'Laminas\Form\Element\Month',
        'monthselect'   => 'Laminas\Form\Element\MonthSelect',
        'multicheckbox' => 'Laminas\Form\Element\MultiCheckbox',
        'number'        => 'Laminas\Form\Element\Number',
        'password'      => 'Laminas\Form\Element\Password',
        'radio'         => 'Laminas\Form\Element\Radio',
        'range'         => 'Laminas\Form\Element\Range',
        'select'        => 'Laminas\Form\Element\Select',
        'submit'        => 'Laminas\Form\Element\Submit',
        'text'          => 'Laminas\Form\Element\Text',
        'textarea'      => 'Laminas\Form\Element\Textarea',
        'time'          => 'Laminas\Form\Element\Time',
        'url'           => 'Laminas\Form\Element\Url',
        'week'          => 'Laminas\Form\Element\Week',
    );

    /**
     * Don't share form elements by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addInitializer(array($this, 'injectFactory'));
    }

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param $element
     */
    public function injectFactory($element)
    {
        if ($element instanceof FormFactoryAwareInterface) {
            $factory = $element->getFormFactory();
            $factory->setFormElementManager($this);

            if ($this->serviceLocator instanceof ServiceLocatorInterface
                && $this->serviceLocator->has('InputFilterManager')
            ) {
                $inputFilters = $this->serviceLocator->get('InputFilterManager');
                $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
            }
        }
    }

    /**
     * Validate the plugin
     *
     * Checks that the element is an instance of ElementInterface
     *
     * @param  mixed $plugin
     * @throws Exception\InvalidElementException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        // Hook to perform various initialization, when the element is not created through the factory
        if ($plugin instanceof InitializableInterface) {
            $plugin->init();
        }

        if ($plugin instanceof ElementInterface) {
            return; // we're okay
        }

        throw new Exception\InvalidElementException(sprintf(
            'Plugin of type %s is invalid; must implement Laminas\Form\ElementInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
