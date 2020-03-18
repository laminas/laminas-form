<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Form\FormElementManager;

use Interop\Container\ContainerInterface;
use Laminas\Form\Element;
use Laminas\Form\ElementFactory;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Form\FormFactoryAwareInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\Stdlib\InitializableInterface;

use function array_push;
use function array_search;
use function array_unshift;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

/**
 * laminas-servicemanager v2-compatible plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class FormElementManagerV2Polyfill extends AbstractPluginManager
{
    use FormElementManagerTrait;

    /**
     * Aliases for default set of helpers
     *
     * @var array
     */
    protected $aliases = [
        'button'         => Element\Button::class,
        'captcha'        => Element\Captcha::class,
        'checkbox'       => Element\Checkbox::class,
        'collection'     => Element\Collection::class,
        'color'          => Element\Color::class,
        'csrf'           => Element\Csrf::class,
        'date'           => Element\Date::class,
        'dateselect'     => Element\DateSelect::class,
        'datetime'       => Element\DateTime::class,
        'datetimelocal'  => Element\DateTimeLocal::class,
        'datetimeselect' => Element\DateTimeSelect::class,
        'element'        => Element::class,
        'email'          => Element\Email::class,
        'fieldset'       => Fieldset::class,
        'file'           => Element\File::class,
        'form'           => Form::class,
        'hidden'         => Element\Hidden::class,
        'image'          => Element\Image::class,
        'month'          => Element\Month::class,
        'monthselect'    => Element\MonthSelect::class,
        'multicheckbox'  => Element\MultiCheckbox::class,
        'number'         => Element\Number::class,
        'password'       => Element\Password::class,
        'radio'          => Element\Radio::class,
        'range'          => Element\Range::class,
        'search'         => Element\Search::class,
        'select'         => Element\Select::class,
        'submit'         => Element\Submit::class,
        'tel'            => Element\Tel::class,
        'text'           => Element\Text::class,
        'textarea'       => Element\Textarea::class,
        'time'           => Element\Time::class,
        'url'            => Element\Url::class,
        'week'           => Element\Week::class,

        // Legacy Zend Framework aliases
        \Zend\Form\Element\Button::class => Element\Button::class,
        \Zend\Form\Element\Captcha::class => Element\Captcha::class,
        \Zend\Form\Element\Checkbox::class => Element\Checkbox::class,
        \Zend\Form\Element\Collection::class => Element\Collection::class,
        \Zend\Form\Element\Color::class => Element\Color::class,
        \Zend\Form\Element\Csrf::class => Element\Csrf::class,
        \Zend\Form\Element\Date::class => Element\Date::class,
        \Zend\Form\Element\DateSelect::class => Element\DateSelect::class,
        \Zend\Form\Element\DateTime::class => Element\DateTime::class,
        \Zend\Form\Element\DateTimeLocal::class => Element\DateTimeLocal::class,
        \Zend\Form\Element\DateTimeSelect::class => Element\DateTimeSelect::class,
        \Zend\Form\Element::class => Element::class,
        \Zend\Form\Element\Email::class => Element\Email::class,
        \Zend\Form\Fieldset::class => Fieldset::class,
        \Zend\Form\Element\File::class => Element\File::class,
        \Zend\Form\Form::class => Form::class,
        \Zend\Form\Element\Hidden::class => Element\Hidden::class,
        \Zend\Form\Element\Image::class => Element\Image::class,
        \Zend\Form\Element\Month::class => Element\Month::class,
        \Zend\Form\Element\MonthSelect::class => Element\MonthSelect::class,
        \Zend\Form\Element\MultiCheckbox::class => Element\MultiCheckbox::class,
        \Zend\Form\Element\Number::class => Element\Number::class,
        \Zend\Form\Element\Password::class => Element\Password::class,
        \Zend\Form\Element\Radio::class => Element\Radio::class,
        \Zend\Form\Element\Range::class => Element\Range::class,
        \Zend\Form\Element\Search::class => Element\Search::class,
        \Zend\Form\Element\Select::class => Element\Select::class,
        \Zend\Form\Element\Submit::class => Element\Submit::class,
        \Zend\Form\Element\Tel::class => Element\Tel::class,
        \Zend\Form\Element\Text::class => Element\Text::class,
        \Zend\Form\Element\Textarea::class => Element\Textarea::class,
        \Zend\Form\Element\Time::class => Element\Time::class,
        \Zend\Form\Element\Url::class => Element\Url::class,
        \Zend\Form\Element\Week::class => Element\Week::class,

        // v2 normalized FQCNs
        'zendformelementbutton' => Element\Button::class,
        'zendformelementcaptcha' => Element\Captcha::class,
        'zendformelementcheckbox' => Element\Checkbox::class,
        'zendformelementcollection' => Element\Collection::class,
        'zendformelementcolor' => Element\Color::class,
        'zendformelementcsrf' => Element\Csrf::class,
        'zendformelementdate' => Element\Date::class,
        'zendformelementdateselect' => Element\DateSelect::class,
        'zendformelementdatetime' => Element\DateTime::class,
        'zendformelementdatetimelocal' => Element\DateTimeLocal::class,
        'zendformelementdatetimeselect' => Element\DateTimeSelect::class,
        'zendformelement' => Element::class,
        'zendformelementemail' => Element\Email::class,
        'zendformfieldset' => Fieldset::class,
        'zendformelementfile' => Element\File::class,
        'zendformform' => Form::class,
        'zendformelementhidden' => Element\Hidden::class,
        'zendformelementimage' => Element\Image::class,
        'zendformelementmonth' => Element\Month::class,
        'zendformelementmonthselect' => Element\MonthSelect::class,
        'zendformelementmulticheckbox' => Element\MultiCheckbox::class,
        'zendformelementnumber' => Element\Number::class,
        'zendformelementpassword' => Element\Password::class,
        'zendformelementradio' => Element\Radio::class,
        'zendformelementrange' => Element\Range::class,
        'zendformelementsearch' => Element\Search::class,
        'zendformelementselect' => Element\Select::class,
        'zendformelementsubmit' => Element\Submit::class,
        'zendformelementtel' => Element\Tel::class,
        'zendformelementtext' => Element\Text::class,
        'zendformelementtextarea' => Element\Textarea::class,
        'zendformelementtime' => Element\Time::class,
        'zendformelementurl' => Element\Url::class,
        'zendformelementweek' => Element\Week::class,
    ];

    /**
     * Factories for default set of helpers
     *
     * @var array
     */
    protected $factories = [
        Element\Button::class         => ElementFactory::class,
        Element\Captcha::class        => ElementFactory::class,
        Element\Checkbox::class       => ElementFactory::class,
        Element\Collection::class     => ElementFactory::class,
        Element\Color::class          => ElementFactory::class,
        Element\Csrf::class           => ElementFactory::class,
        Element\Date::class           => ElementFactory::class,
        Element\DateSelect::class     => ElementFactory::class,
        Element\DateTime::class       => ElementFactory::class,
        Element\DateTimeLocal::class  => ElementFactory::class,
        Element\DateTimeSelect::class => ElementFactory::class,
        Element::class                => ElementFactory::class,
        Element\Email::class          => ElementFactory::class,
        Fieldset::class               => ElementFactory::class,
        Element\File::class           => ElementFactory::class,
        Form::class                   => ElementFactory::class,
        Element\Hidden::class         => ElementFactory::class,
        Element\Image::class          => ElementFactory::class,
        Element\Month::class          => ElementFactory::class,
        Element\MonthSelect::class    => ElementFactory::class,
        Element\MultiCheckbox::class  => ElementFactory::class,
        Element\Number::class         => ElementFactory::class,
        Element\Password::class       => ElementFactory::class,
        Element\Radio::class          => ElementFactory::class,
        Element\Range::class          => ElementFactory::class,
        Element\Search::class         => ElementFactory::class,
        Element\Select::class         => ElementFactory::class,
        Element\Submit::class         => ElementFactory::class,
        Element\Tel::class            => ElementFactory::class,
        Element\Text::class           => ElementFactory::class,
        Element\Textarea::class       => ElementFactory::class,
        Element\Time::class           => ElementFactory::class,
        Element\Url::class            => ElementFactory::class,
        Element\Week::class           => ElementFactory::class,

        // v2 normalized variants
        'laminasformelementbutton'         => ElementFactory::class,
        'laminasformelementcaptcha'        => ElementFactory::class,
        'laminasformelementcheckbox'       => ElementFactory::class,
        'laminasformelementcollection'     => ElementFactory::class,
        'laminasformelementcolor'          => ElementFactory::class,
        'laminasformelementcsrf'           => ElementFactory::class,
        'laminasformelementdate'           => ElementFactory::class,
        'laminasformelementdateselect'     => ElementFactory::class,
        'laminasformelementdatetime'       => ElementFactory::class,
        'laminasformelementdatetimelocal'  => ElementFactory::class,
        'laminasformelementdatetimeselect' => ElementFactory::class,
        'laminasformelement'               => ElementFactory::class,
        'laminasformelementemail'          => ElementFactory::class,
        'laminasformfieldset'              => ElementFactory::class,
        'laminasformelementfile'           => ElementFactory::class,
        'laminasformform'                  => ElementFactory::class,
        'laminasformelementhidden'         => ElementFactory::class,
        'laminasformelementimage'          => ElementFactory::class,
        'laminasformelementmonth'          => ElementFactory::class,
        'laminasformelementmonthselect'    => ElementFactory::class,
        'laminasformelementmulticheckbox'  => ElementFactory::class,
        'laminasformelementnumber'         => ElementFactory::class,
        'laminasformelementpassword'       => ElementFactory::class,
        'laminasformelementradio'          => ElementFactory::class,
        'laminasformelementrange'          => ElementFactory::class,
        'laminasformelementsearch'         => ElementFactory::class,
        'laminasformelementselect'         => ElementFactory::class,
        'laminasformelementsubmit'         => ElementFactory::class,
        'laminasformelementtel'            => ElementFactory::class,
        'laminasformelementtext'           => ElementFactory::class,
        'laminasformelementtextarea'       => ElementFactory::class,
        'laminasformelementtime'           => ElementFactory::class,
        'laminasformelementurl'            => ElementFactory::class,
        'laminasformelementweek'           => ElementFactory::class,
    ];

    /**
     * Don't share form elements by default (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Don't share form elements by default (v2)
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Interface all plugins managed by this class must implement.
     * @var string
     */
    protected $instanceOf = ElementInterface::class;

    /**
     * Constructor
     *
     * Overrides parent constructor in order to add the initializer methods injectFactory()
     * and callElementInit().
     *
     * @param null|ConfigInterface|ContainerInterface $configOrContainerInstance
     * @param array $v3config If $configOrContainerInstance is a container, this
     *     value will be passed to the parent constructor.
     */
    public function __construct($configInstanceOrParentLocator = null, array $v3config = [])
    {
        // Provide default initializers, ensuring correct order
        array_unshift($this->initializers, [$this, 'injectFactory']);
        array_push($this->initializers, [$this, 'callElementInit']);

        parent::__construct($configInstanceOrParentLocator, $v3config);
    }

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param mixed $instance Instance to inspect and potentially inject.
     * @param ContainerInterface $container Container passed to initializer.
     */
    public function injectFactory($instance, ContainerInterface $container)
    {
        // Need to retrieve the parent container
        $container = $container->getServiceLocator() ?: $container;

        if (! $instance instanceof FormFactoryAwareInterface) {
            return;
        }

        $factory = $instance->getFormFactory();
        $factory->setFormElementManager($this);

        if ($container && $container->has('InputFilterManager')) {
            $inputFilters = $container->get('InputFilterManager');
            $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
        }
    }

    /**
     * Call init() on any element that implements InitializableInterface
     *
     * @param mixed $instance Instance to inspect and optionally initialize.
     * @param ContainerInterface $container
     */
    public function callElementInit($instance, ContainerInterface $container)
    {
        if ($instance instanceof InitializableInterface) {
            $instance->init();
        }
    }

    /**
     * Override setInvokableClass
     *
     * Overrides setInvokableClass to:
     *
     * - add a factory mapping $invokableClass to ElementFactory::class
     * - alias $name to $invokableClass
     *
     * @param string $name
     * @param string $invokableClass
     * @param null|bool $shared Ignored.
     * @return $this
     */
    public function setInvokableClass($name, $invokableClass, $shared = null)
    {
        if (! $this->has($invokableClass)) {
            $this->setFactory($invokableClass, ElementFactory::class);
        }

        if ($invokableClass !== $name) {
            $this->setAlias($name, $invokableClass);
        }

        return $this;
    }

    /**
     * Validate the plugin is of the expected type.
     *
     * @param mixed $plugin
     * @throws Exception\InvalidElementException
     */
    public function validatePlugin($plugin)
    {
        if (! $plugin instanceof $this->instanceOf) {
            throw new Exception\InvalidElementException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                is_object($plugin) ? get_class($plugin) : gettype($plugin)
            ));
        }
    }

    /**
     * Overrides parent::addInitializer in order to ensure default initializers are in expected positions.
     *
     * Always pushes `injectFactory` to top of initializer stack, and
     * `callElementInit` to the bottom.
     *
     * {@inheritDoc}
     */
    public function addInitializer($initializer, $topOfStack = true)
    {
        $firstInitializer = [$this, 'injectFactory'];
        $lastInitializer  = [$this, 'callElementInit'];

        foreach ([$firstInitializer, $lastInitializer] as $default) {
            if (false === ($index = array_search($default, $this->initializers))) {
                continue;
            }
            unset($this->initializers[$index]);
        }

        parent::addInitializer($initializer, $topOfStack);

        array_unshift($this->initializers, $firstInitializer);
        array_push($this->initializers, $lastInitializer);

        return $this;
    }
}
