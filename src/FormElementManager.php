<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\Stdlib\InitializableInterface;

/**
 * Plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class FormElementManager extends AbstractPluginManager
{
    /**
     * Aliases for default set of helpers
     *
     * @var array
     */
    protected $aliases = [
        'button'         => Element\Button::class,
        'Button'         => Element\Button::class,
        'captcha'        => Element\Captcha::class,
        'Captcha'        => Element\Captcha::class,
        'checkbox'       => Element\Checkbox::class,
        'Checkbox'       => Element\Checkbox::class,
        'collection'     => Element\Collection::class,
        'Collection'     => Element\Collection::class,
        'color'          => Element\Color::class,
        'Color'          => Element\Color::class,
        'csrf'           => Element\Csrf::class,
        'Csrf'           => Element\Csrf::class,
        'date'           => Element\Date::class,
        'Date'           => Element\Date::class,
        'dateselect'     => Element\DateSelect::class,
        'dateSelect'     => Element\DateSelect::class,
        'DateSelect'     => Element\DateSelect::class,
        'datetime'       => Element\DateTime::class,
        'dateTime'       => Element\DateTime::class,
        'DateTime'       => Element\DateTime::class,
        'datetimelocal'  => Element\DateTimeLocal::class,
        'dateTimeLocal'  => Element\DateTimeLocal::class,
        'DateTimeLocal'  => Element\DateTimeLocal::class,
        'datetimeselect' => Element\DateTimeSelect::class,
        'dateTimeSelect' => Element\DateTimeSelect::class,
        'DateTimeSelect' => Element\DateTimeSelect::class,
        'element'        => Element::class,
        'Element'        => Element::class,
        'email'          => Element\Email::class,
        'Email'          => Element\Email::class,
        'fieldset'       => Fieldset::class,
        'Fieldset'       => Fieldset::class,
        'file'           => Element\File::class,
        'File'           => Element\File::class,
        'form'           => Form::class,
        'Form'           => Form::class,
        'hidden'         => Element\Hidden::class,
        'Hidden'         => Element\Hidden::class,
        'image'          => Element\Image::class,
        'Image'          => Element\Image::class,
        'month'          => Element\Month::class,
        'Month'          => Element\Month::class,
        'monthselect'    => Element\MonthSelect::class,
        'monthSelect'    => Element\MonthSelect::class,
        'MonthSelect'    => Element\MonthSelect::class,
        'multicheckbox'  => Element\MultiCheckbox::class,
        'multiCheckbox'  => Element\MultiCheckbox::class,
        'MultiCheckbox'  => Element\MultiCheckbox::class,
        'multiCheckBox'  => Element\MultiCheckbox::class,
        'MultiCheckBox'  => Element\MultiCheckbox::class,
        'number'         => Element\Number::class,
        'Number'         => Element\Number::class,
        'password'       => Element\Password::class,
        'Password'       => Element\Password::class,
        'radio'          => Element\Radio::class,
        'Radio'          => Element\Radio::class,
        'range'          => Element\Range::class,
        'Range'          => Element\Range::class,
        'select'         => Element\Select::class,
        'Select'         => Element\Select::class,
        'submit'         => Element\Submit::class,
        'Submit'         => Element\Submit::class,
        'text'           => Element\Text::class,
        'Text'           => Element\Text::class,
        'textarea'       => Element\Textarea::class,
        'Textarea'       => Element\Textarea::class,
        'time'           => Element\Time::class,
        'Time'           => Element\Time::class,
        'url'            => Element\Url::class,
        'Url'            => Element\Url::class,
        'week'           => Element\Week::class,
        'Week'           => Element\Week::class,
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
        Element\Select::class         => ElementFactory::class,
        Element\Submit::class         => ElementFactory::class,
        Element\Text::class           => ElementFactory::class,
        Element\Textarea::class       => ElementFactory::class,
        Element\Time::class           => ElementFactory::class,
        Element\Url::class            => ElementFactory::class,
        Element\Week::class           => ElementFactory::class,

        // v2 normalized variants

        'zendformelementbutton'         => ElementFactory::class,
        'zendformelementcaptcha'        => ElementFactory::class,
        'zendformelementcheckbox'       => ElementFactory::class,
        'zendformelementcollection'     => ElementFactory::class,
        'zendformelementcolor'          => ElementFactory::class,
        'zendformelementcsrf'           => ElementFactory::class,
        'zendformelementdate'           => ElementFactory::class,
        'zendformelementdateselect'     => ElementFactory::class,
        'zendformelementdatetime'       => ElementFactory::class,
        'zendformelementdatetimelocal'  => ElementFactory::class,
        'zendformelementdatetimeselect' => ElementFactory::class,
        'zendformelement'               => ElementFactory::class,
        'zendformelementemail'          => ElementFactory::class,
        'zendformfieldset'              => ElementFactory::class,
        'zendformelementfile'           => ElementFactory::class,
        'zendformform'                  => ElementFactory::class,
        'zendformelementhidden'         => ElementFactory::class,
        'zendformelementimage'          => ElementFactory::class,
        'zendformelementmonth'          => ElementFactory::class,
        'zendformelementmonthselect'    => ElementFactory::class,
        'zendformelementmulticheckbox'  => ElementFactory::class,
        'zendformelementnumber'         => ElementFactory::class,
        'zendformelementpassword'       => ElementFactory::class,
        'zendformelementradio'          => ElementFactory::class,
        'zendformelementrange'          => ElementFactory::class,
        'zendformelementselect'         => ElementFactory::class,
        'zendformelementsubmit'         => ElementFactory::class,
        'zendformelementtext'           => ElementFactory::class,
        'zendformelementtextarea'       => ElementFactory::class,
        'zendformelementtime'           => ElementFactory::class,
        'zendformelementurl'            => ElementFactory::class,
        'zendformelementweek'           => ElementFactory::class,
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
        $this->initializers[] = [$this, 'injectFactory'];
        $this->initializers[] = [$this, 'callElementInit'];

        parent::__construct($configInstanceOrParentLocator, $v3config);
    }

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param mixed $first ContainerInterface when used under zend-servicemanager
     *     v3, element or form when under v2.
     * @param mixed $second Element or form when used under zend-servicemanager
     *     v3, ContainerInterface when under v2.
     */
    public function injectFactory($first, $second)
    {
        if ($first instanceof ContainerInterface) {
            // Container is the parent container under v3
            $container = $first;
            $instance = $second;
        } else {
            // Need to retrieve the parent container under v2
            $container = $second->getServiceLocator() ?: $second;
            $instance = $first;
        }

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
     * @param mixed $first ContainerInterface when used under zend-servicemanager
     *     v3, element or form when under v2.
     * @param mixed $second Element or form when used under zend-servicemanager
     *     v3, ContainerInterface when under v2.
     */
    public function callElementInit($first, $second)
    {
        $instance = ($first instanceof ContainerInterface)
            ? $second // v3
            : $first; // v2

        if ($instance instanceof InitializableInterface) {
            $instance->init();
        }
    }

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param  mixed $plugin
     * @throws InvalidServiceException
     * @return void
     */
    public function validate($plugin)
    {
        if (! $plugin instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))
            ));
        }
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $plugin
     * @throws Exception\InvalidElementException
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidElementException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  string|array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = [], $usePeeringServiceManagers = true)
    {
        if (is_string($options)) {
            $options = ['name' => $options];
        }
        return parent::get($name, $options, $usePeeringServiceManagers);
    }

    /**
     * Try to pull hydrator from the creation context, or instantiates it from its name
     *
     * @param  string $hydratorName
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getHydratorFromName($hydratorName)
    {
        $services = isset($this->creationContext)
            ? $this->creationContext // v3
            : $this->serviceLocator; // v2

        if ($services && $services->has('HydratorManager')) {
            $hydrators = $services->get('HydratorManager');
            if ($hydrators->has($hydratorName)) {
                return $hydrators->get($hydratorName);
            }
        }

        if ($services && $services->has($hydratorName)) {
            return $services->get($hydratorName);
        }

        if (! class_exists($hydratorName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string hydrator name to be a valid class name; received "%s"',
                $hydratorName
            ));
        }

        $hydrator = new $hydratorName;
        return $hydrator;
    }

    /**
     * Try to pull factory from the creation context, or instantiates it from its name
     *
     * @param  string $factoryName
     * @return mixed
     * @throws Exception\DomainException
     */
    public function getFactoryFromName($factoryName)
    {
        $services = isset($this->creationContext)
            ? $this->creationContext // v3
            : $this->serviceLocator; // v2

        if ($services && $services->has($factoryName)) {
            return $services->get($factoryName);
        }

        if (! class_exists($factoryName)) {
            throw new Exception\DomainException(sprintf(
                'Expects string factory name to be a valid class name; received "%s"',
                $factoryName
            ));
        }

        $factory = new $factoryName;
        return $factory;
    }
}
